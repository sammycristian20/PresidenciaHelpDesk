<?php


namespace App\Traits;

use App\User;
use App\Location\Models\Location;
use App\Model\helpdesk\Form\FormField;
use App\Repositories\FormRepository;
use App\Plugins\Ldap\Traits\UserDependencyHandler;

trait UserImport
{
    use UserDependencyHandler, UserVerificationHelper;

    /**
     * Attributes which defines the user. user_name is an attribute but department is a property
     * @var string[]
     */
    public static $userAttributes = ['user_name','email','phone_number','first_name','last_name','role','import_identifier','location'];

    /**
     * Properties which user can own. user_name is an attribute but department is a property
     * @var string[]
     */
    public static $userProperties = ['department', 'organization', 'org_dept','is_organization_manager', 'is_department_manager'];

    /**
     * Determines whether email registration email be sent to user after import
     * @var bool
     */
    protected $sendEmailNotification = false;

    /**
     * Checks if a user already exists. If no, then creates a new user
     * @param object $users array of ldap users
     * @return null
     */
    protected function handleBulk($users)
    {
        foreach ($users as $userObject) {
            $username = $this->getAttributeValue('user_name', $userObject);

            $email = $this->getAttributeValue('email', $userObject);

            $identifier = $this->getAttributeValue('import_identifier', $userObject);

            if ($this->isAllowedToCreatedUser($username, $email, $identifier)) {
                $user = $this->getUserFromDB($username, $email, $identifier);

                //based on there 2 figure out if user is being created or being updated
                $user ? $this->updateOrCreateUser($userObject, $user, false)
                    : $this->updateOrCreateUser($userObject, new User, true);
            }
        }
    }

     /**
     * Saves location fetched from LDAP in database(if the location is not present);returns it
     * @param string $location
     * @return string
     */
    private function getLocation($location = '')
    {
        $userLocation = '';

        $locationQuery = Location::where('title','LIKE', $location);

        if ($location) {
            if (!$locationQuery->count()) {
                $createdLocation = Location::create(['title' => ucfirst($location)]);
                $userLocation = $createdLocation->id;
            } else {
                $userLocation = $locationQuery->value('id');
            }
        }
        return $userLocation;
    }

    /**
     * update only those attributes which are marked as overwrite
     * NOTE: type hinting isn't used to facilitate easy mocking of the method
     * @param object $userObject single ldap user
     * @param User $user it must a user instance of an existing user of a new instance altoghther
     * @param $isCreating
     * @return User
     */
    private function updateOrCreateUser($userObject, User $user, $isCreating)
    {
        foreach (self::$userAttributes as $field) {
            if ($isCreating || $this->isOverwriteAllowed($field)) {
                    //getAttribute method takes care of which value has to be give (default or non-default)
                    if ($field === 'location') {
                        $user->$field = $this->getLocation($this->getAttributeValue('location', $userObject));
                     } else {
                        $user->$field = $this->getAttributeValue($field, $userObject);
                    }
            }
        }

        $this->handleUserRoleUpdate($user);

        // if user is getting created, it should be active. If updating, it should remain the same
        // status as before
        $user->active = $isCreating ? 1 : $user->active;

        $user->save();

        if ($user->role == 'user') {
            //handle organization and organization department
            $this->handleOrganization($userObject, $user, $isCreating);
        } else {
            //handle department and default permissions
            $this->handleDepartment($userObject, $user, $isCreating);
        }

        $this->updateCustomFieldValues($userObject, $user, $isCreating);

        if ($isCreating && $this->sendEmailNotification) {
            $this->sendRegistrationMails($user);
        }
        /**
         * We Will set entities like mobile and email as unverified if they are not imported
         * from AD or CSV. Reason we call are calling this method is because default values of
         * verify columns is set to 1 and this class is not dealing with those entities.
         */
        $this->setEntitiesUnverifiedByModel($user);
        /**
         * Setting entities as verified if imported from AD/CSV. As default values for verify
         * entity column is one still we are calling this method explicitly as User model has
         * mutators which are changing the values if data is newly inserted or updated in the
         * email and mobile columns.
         */
        $this->setEntitiesVerifiedByModel($user);
        
        return $user;
    }

    /**
     * Updates custom field values for a user
     * @param $userObject
     * @param User $user
     * @param $isCreating
     */
    private function updateCustomFieldValues($userObject, User $user, $isCreating)
    {
        foreach (FormRepository::getUserCustomFieldList() as $formField) {
            try {
                $formFieldIdentifier = "custom_".$formField->id;
                if ($isCreating || $this->isOverwriteAllowed($formFieldIdentifier)) {
                    //getAttribute method takes care of which value has to be give (default or non-default)
                    $customValue = $this->getAttributeValue($formFieldIdentifier, $userObject);
                    if (!$customValue) {
                        // delete that key custom value table
                        $user->customFieldValues()->where('form_field_id', $formField->id)->delete();
                    } else {
                        $user->customFieldValues()->updateOrCreate(['form_field_id'=> $formField->id], ['value'=> $customValue]);
                    }
                }
            } catch (\Exception $e) {
                // ignore, so that rest of the fields can be saved
            }
        }
    }

    /**
     * handles all organization logic including overwrite logic
     * @param  object  $userObject  ldap user instance
     * @param  User  $user                faveo user instance
     * @param  boolean $isCreating        if a new record is getting created
     * @return array                      array of organizationIds
     */
    private function handleOrganization($userObject, $user, $isCreating)
    {
        //check is overwrite is ON for organization
        $organizationIds = $this->getAttributeValue('organization', $userObject);

        if ($isCreating || $this->isOverwriteAllowed('organization')) {
            $this->assignOrganization($user, $organizationIds);
            //call organization department here itself
            $this->handleOrgDept($userObject, $user, $isCreating, $organizationIds);
        }

        // (organization-overwrite, organization-manager-overwrite)
        //
        // CASE 1: (true, true) =>
        //  - If organization is coming as null, it should not work at all.
        //  - If deparment is present and present in DB also, it should make that agent as organization manager
        //    of that organization and remove old ones.
        //  - If organization is new, it should be created and assigned the manager
        //
        // CASE 2: (true, false) =>
        //  - If organization manager overwrite is false, there are zero problems, since it will work
        //   the same way it was working before
        //
        // CASE 3: (false, true) =>
        //  - If organization is coming as null, zero problems.
        //  - If organization is coming as non-null, that agent doesn't belong to that organization,
        //   should that organization be created ? If not, then what is the purpose of having overwite
        //   in is_organization_manager?
        //  - If organization is coming as non-null, that agent belongs to that organization,
        //   He should be made organization manager of that organization?
        //
        //  CASE 4: (false, false) :  should work only in case of creating new users
        //
        // assign organization manager if in creating mode or overwrite for is_organization_manager is on
        if ($isCreating || $this->isOverwriteAllowed('is_organization_manager')) {
            // get organization IDs, assign organization manager
            // if overwrite for organization is false, and overwrite for is_organization_manager
            // is true, there will be no organization. In that case get organization and if
            // current agent belongs to that organization it should be made manager else not
            $isOrganisationManager = (bool) $this->getAttributeValue('is_organization_manager', $userObject);
            $this->makeOrganizationManager($user, $organizationIds, $isOrganisationManager);
        }
    }

    /**
     * creates org dept and link user to it
     * @param  object $userObject
     * @param  User $user
     * @param  boolean $isCreating
     * @param  array $organizationIds
     * @return null
     */
    private function handleOrgDept($userObject, $user, $isCreating, $organizationIds)
    {
        //if organization department module is on
        //organization department should only be allowed to overwrite if organization is allowed to overwrite
        //micro_organization_status
        if (isOrgDeptModuleEnabled() && count($organizationIds) && ($isCreating || $this->isOverwriteAllowed('org_dept'))) {
            $adAttribute = $this->getThirdPartyAttributeByFaveoAttribute('org_dept');
            $adAttributeForOrganization = $this->getThirdPartyAttributeByFaveoAttribute('organization');

            if ($adAttribute != 'FAVEO DEFAULT' && $adAttributeForOrganization != 'FAVEO DEFAULT') {
                //creating organization department with first organization
                //REASON: organization department will be created only for one organization
                //        creation of multiple organization deparment is allowed only if it is 'FAVEO DEFAULT'
                //        but for FAVEO DEFAULT we dont create organization department
                //PROBELM: what if organization is default but organization department is not?
                //EXPLAINATION: in that case whatever organization is passed to it, the first out of those
                //        will be used to create organization department as organization department cannot belong to multiple organization
                $attributeValue = $this->getAttributeValue('org_dept', $userObject);

                $orgDeptId = $attributeValue ? $this->createOrUpdateOrgDept($organizationIds[0], $attributeValue) : 0;

                if ($orgDeptId) {
                    //NOTE: it is a workaround for current system to work, as organization_id ideally
                    //      should not be stored in connecting table but just organization_dept table
                    //      this could be achieved
                    $this->assignOrgDept($user, $orgDeptId, $organizationIds[0]);
                }
            }
        }
    }

    /**
     * handles all department logic including overwrite logic. So once this method is called,
     * there nothing to worry about handling department
     * @param  object  $ldapUserInstance ldap user instance
     * @param  User  $user
     * @param  boolean $isCreating       if a new record is being created
     * @return null
     */
    private function handleDepartment($ldapUserInstance, $user, $isCreating)
    {
        // check is overwrite is ON for department
        // If it is in creating mode, it should simply create the department and assign agent to it
        // If overwrite is true, it should overwrite agent's department by deleting
        // old entries and creating new ones. If an agent is  manually assigned to a department,
        // it will be overwritten by LDAP sync
        $departmentIds = $this->getAttributeValue('department', $ldapUserInstance);
        if ($isCreating || $this->isOverwriteAllowed('department')) {
            $this->assignDepartment($user, $departmentIds);

            $this->handleDefaultPermissions($user, $isCreating);
        }

        // (department-overwrite, deparment-manager-overwrite)
        //
        // CASE 1: (true, true) =>
        //  - If department is coming as null, it should not work at all.
        //  - If deparment is present and present in DB also, it should make that agent as department manager
        //    of that department and remove old ones.
        //  - If department is new, it should be created and assigned the manager
        //
        // CASE 2: (true, false) =>
        //  - If department manager overwrite is false, there are zero problems, since it will work
        //   the same way it was working before
        //
        // CASE 3: (false, true) =>
        //  - If department is coming as null, zero problems.
        //  - If department is coming as non-null, that agent doesn't belong to that department,
        //   should that department be created ? If not, then what is the purpose of having overwite
        //   in is_department_manager?
        //  - If department is coming as non-null, that agent belongs to that department,
        //   He should be made department manager of that department?
        //
        //  CASE 4: (false, false) :  should work only in case of creating new users
        //
        // assign department manager if in creating mode or overwrite for is_department_manager is on
        if ($isCreating || $this->isOverwriteAllowed('is_department_manager')) {
            // get department IDs, assign department manager
            // if overwrite for department is false, and overwrite for is_department_manager
            // is true, there will be no department. In that case get department and if
            // current agent belongs to that department it should be made manager else not
            // pass the value if needed to make department manager or not
            $isDepartmentManager = (bool) $this->getAttributeValue('is_department_manager', $ldapUserInstance);

            $this->makeDepartmentManager($user, $departmentIds, $isDepartmentManager);
        }
    }

    /**
     * handles default permissions
     * @param  User $user
     * @param  bool $isCreating
     * @return null
     */
    private function handleDefaultPermissions(User $user, bool $isCreating)
    {
        //check is overwrite is ON for organization
        if ($isCreating) {
            $this->assignDefaultPermissions($user);
        }
    }

    /**
     * Gets list of available faveo attributes for the import in their default values
     * @return \Illuminate\Support\Collection
     */
    public function getFaveoAttributeList()
    {
        $defaultAttributes = collect(array_merge(self::$userAttributes, self::$userProperties))
            ->map(function($element){
                $attributeObject = (object)[];
                $attributeObject->name = $element;
                $attributeObject->label = \Lang::get("lang.$element");
                $attributeObject->mapped_to = 'Do not Import';
                $attributeObject->overwrite = false;
                $attributeObject->overwriteable = true;
                $attributeObject->description = trans('lang.'.$element.'_description');
                return $attributeObject;
            });

        // org_dept_should not be considered if it is off
        if (!isOrgDeptModuleEnabled()) {
            $defaultAttributes = $defaultAttributes->where('name', '!=', 'org_dept');
        }

        // need to have the labels too
        $customAttributes = FormRepository::getUserCustomFieldList()
            ->map(function($element){
                $attributeObject = (object)[];
                $attributeObject->name = "custom_$element->id";
                $attributeObject->label = $element->getLabelAttribute();
                $attributeObject->mapped_to = 'Do not Import';
                $attributeObject->overwrite = false;
                $attributeObject->overwriteable = true;
                return $attributeObject;
            });

        return $defaultAttributes->merge($customAttributes);
    }

    /**
     * Gets the value of third party attribute
     * @param $attribute
     * @param $userObject
     * @return string|null
     */
    abstract protected function getAttributeValue($attribute, $userObject) : ?string;

    /**
     * Tells if certain attribute has overwrite allowed of not
     * @param $attribute
     * @return bool
     */
    abstract protected function isOverwriteAllowed($attribute) : bool;

    /**
     * Gets third party attribute based on faveo attribute (mapping)
     * @param $faveoAttribute
     * @return string|null
     */
    abstract protected function getThirdPartyAttributeByFaveoAttribute($faveoAttribute) : ?string;
}