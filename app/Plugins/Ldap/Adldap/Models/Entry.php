<?php

namespace App\Plugins\Ldap\Adldap\Models;

use DateTime;
use App\Plugins\Ldap\Adldap\Utilities;

class Entry extends Model
{
    /**
     * Returns true / false if the current model is writeable
     * by checking its instance type integer.
     *
     * @return bool
     */
    public function isWriteable()
    {
        return (int) $this->getInstanceType() === 4;
    }

    /**
     * Returns the model's name. An AD alias for the CN attribute.
     *
     * https://msdn.microsoft.com/en-us/library/ms675449(v=vs.85).aspx
     *
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute($this->schema->name(), 0);
    }

    /**
     * Sets the model's name.
     *
     * @param string $name
     *
     * @return Model
     */
    public function setName($name)
    {
        return $this->setAttribute($this->schema->name(), $name, 0);
    }

    /**
     * Returns the model's samaccountname.
     *
     * https://msdn.microsoft.com/en-us/library/ms679635(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountName()
    {
        return $this->getAttribute($this->schema->accountName(), 0);
    }

    /**
     * Sets the model's samaccountname.
     *
     * @param string $accountName
     *
     * @return Model
     */
    public function setAccountName($accountName)
    {
        return $this->setAttribute($this->schema->accountName(), $accountName, 0);
    }

    /**
     * Returns the model's samaccounttype.
     *
     * https://msdn.microsoft.com/en-us/library/ms679637(v=vs.85).aspx
     *
     * @return string
     */
    public function getAccountType()
    {
        return $this->getAttribute($this->schema->accountType(), 0);
    }

    /**
     * Returns the model's `whenCreated` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680924(v=vs.85).aspx
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getAttribute($this->schema->createdAt(), 0);
    }

    /**
     * Returns the created at time in a mysql formatted date.
     *
     * @return string
     */
    public function getCreatedAtDate()
    {
        return (new DateTime())->setTimestamp($this->getCreatedAtTimestamp())->format($this->dateFormat);
    }

    /**
     * Returns the created at time in a unix timestamp format.
     *
     * @return float
     */
    public function getCreatedAtTimestamp()
    {
        return DateTime::createFromFormat('YmdHis.0Z', $this->getCreatedAt())->getTimestamp();
    }

    /**
     * Returns the model's `whenChanged` time.
     *
     * https://msdn.microsoft.com/en-us/library/ms680921(v=vs.85).aspx
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute($this->schema->updatedAt(), 0);
    }

    /**
     * Returns the updated at time in a mysql formatted date.
     *
     * @return string
     */
    public function getUpdatedAtDate()
    {
        return (new DateTime())->setTimestamp($this->getUpdatedAtTimestamp())->format($this->dateFormat);
    }

    /**
     * Returns the updated at time in a unix timestamp format.
     *
     * @return float
     */
    public function getUpdatedAtTimestamp()
    {
        return DateTime::createFromFormat($this->timestampFormat, $this->getUpdatedAt())->getTimestamp();
    }

    /**
     * Returns the Container of the current Model.
     *
     * https://msdn.microsoft.com/en-us/library/ms679012(v=vs.85).aspx
     *
     * @return Container|Entry|bool
     */
    public function getObjectClass()
    {
        return $this->query->findByDn($this->getObjectCategoryDn());
    }

    /**
     * Returns the CN of the model's object category.
     *
     * @return null|string
     */
    public function getObjectCategory()
    {
        $category = $this->getObjectCategoryArray();

        if (is_array($category) && array_key_exists(0, $category)) {
            return $category[0];
        }
    }

    /**
     * Returns the model's object category DN in an exploded array.
     *
     * @return array|false
     */
    public function getObjectCategoryArray()
    {
        return Utilities::explodeDn($this->getObjectCategoryDn());
    }

    /**
     * Returns the model's object category DN string.
     *
     * @return null|string
     */
    public function getObjectCategoryDn()
    {
        return $this->getAttribute($this->schema->objectCategory(), 0);
    }

    /**
     * Returns the model's object SID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679024(v=vs.85).aspx
     *
     * @return string
     */
    public function getObjectSid()
    {
        return $this->getAttribute($this->schema->objectSid(), 0);
    }

    /**
     * Returns the model's primary group ID.
     *
     * https://msdn.microsoft.com/en-us/library/ms679375(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrimaryGroupId()
    {
        return $this->getAttribute($this->schema->primaryGroupId(), 0);
    }

    /**
     * Returns the model's instance type.
     *
     * https://msdn.microsoft.com/en-us/library/ms676204(v=vs.85).aspx
     *
     * @return int
     */
    public function getInstanceType()
    {
        return $this->getAttribute($this->schema->instanceType(), 0);
    }

    /**
     * Returns the model's GUID.
     *
     * @return string
     */
    public function getGuid()
    {
        return Utilities::binaryGuidToString($this->getAttribute($this->schema->objectGuid(), 0));
    }

    /**
     * Returns the model's SID.
     *
     * @return string
     */
    public function getSid()
    {
        return Utilities::binarySidToString($this->getAttribute($this->schema->objectSid(), 0));
    }

    /**
     * Returns the model's max password age.
     *
     * @return string
     */
    public function getMaxPasswordAge()
    {
        return $this->getAttribute($this->schema->maxPasswordAge(), 0);
    }
    
    public function getDisplayName()
    {
        return $this->getAttribute($this->schema->displayName(), 0);
    }
    public function getTitle()
    {
        return $this->getAttribute($this->schema->title(), 0);
    }
    public function getDepartment()
    {
        return $this->getAttribute($this->schema->department(), 0);
    }
    public function getFirstName()
    {
        return $this->getAttribute($this->schema->firstName(), 0);
    }
    public function getLastName()
    {
        return $this->getAttribute($this->schema->lastName(), 0);
    }
    public function getInfo()
    {
        return $this->getAttribute($this->schema->info(), 0);
    }
    /**
     * Returns the users initials.
     *
     * @return mixed
     */
    public function getInitials()
    {
        return $this->getAttribute($this->schema->initials(), 0);
    }
    /**
     * Returns the users country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getAttribute($this->schema->country(), 0);
    }
    /**
     * Returns the users street address.
     *
     * @return User
     */
    public function getStreetAddress()
    {
        return $this->getAttribute($this->schema->streetAddress(), 0);
    }
    /**
     * Returns the users postal code.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->getAttribute($this->schema->postalCode(), 0);
    }
    /**
     * Returns the users physical delivery office name.
     *
     * @return string
     */
    public function getPhysicalDeliveryOfficeName()
    {
        return $this->getAttribute($this->schema->physicalDeliveryOfficeName(), 0);
    }
    /**
     * Returns the users telephone number.
     *
     * https://msdn.microsoft.com/en-us/library/ms680027(v=vs.85).aspx
     *
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->getAttribute($this->schema->telephone(), 0);
    }
    /**
     * Returns the users locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->getAttribute($this->schema->locale(), 0);
    }
    /**
     * Returns the users company.
     *
     * https://msdn.microsoft.com/en-us/library/ms675457(v=vs.85).aspx
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->getAttribute($this->schema->company(), 0);
    }
    /**
     * Returns the users primary email address.
     *
     * https://msdn.microsoft.com/en-us/library/ms676855(v=vs.85).aspx
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getAttribute($this->schema->email(), 0);
    }
    /**
     * Returns the users email addresses.
     *
     * https://msdn.microsoft.com/en-us/library/ms676855(v=vs.85).aspx
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->getAttribute($this->schema->email());
    }
    /**
     * Returns the users other mailbox attribute.
     *
     * https://msdn.microsoft.com/en-us/library/ms679091(v=vs.85).aspx
     *
     * @return array
     */
    public function getOtherMailbox()
    {
        return $this->getAttribute($this->schema->otherMailbox());
    }
    /**
     * Returns the users mailbox store DN.
     *
     * https://msdn.microsoft.com/en-us/library/aa487565(v=exchg.65).aspx
     *
     * @return string
     */
    public function getHomeMdb()
    {
        return $this->getAttribute($this->schema->homeMdb(), 0);
    }

    /**
     * Returns the users mail nickname.
     *
     * @return string
     */
    public function getMailNickname()
    {
        return $this->getAttribute($this->schema->emailNickname(), 0);
    }

    /**
     * Returns the users principal name.
     *
     * This is usually their email address.
     *
     * https://msdn.microsoft.com/en-us/library/ms680857(v=vs.85).aspx
     *
     * @return string
     */
    public function getUserPrincipalName()
    {
        return $this->getAttribute($this->schema->userPrincipalName(), 0);
    }
    /**
     * Returns the users profile file path.
     *
     * @return string
     */
    public function getProfilePath()
    {
        return $this->getAttribute($this->schema->profilePath(), 0);
    }
    /**
     * Returns the users account expiry date.
     *
     * @return string
     */
    public function getAccountExpiry()
    {
        return $this->getAttribute($this->schema->accountExpires(), 0);
    }
    /**
     * Returns the users thumbnail photo.
     *
     * @return mixed
     */
    public function getThumbnail()
    {
        return $this->getAttribute($this->schema->thumbnail(), 0);
    }
    /**
     * Returns the distinguished name of the user who is the user's manager.
     *
     * @return string
     */
    public function getManager()
    {
        return $this->getAttribute($this->schema->manager(), 0);
    }
    /**
     * Return the employee ID.
     *
     * @return User
     */
    public function getEmployeeId()
    {
        return $this->getAttribute($this->schema->employeeId(), 0);
    }
    /**
     * Return the personal title.
     *
     * @return User
     */
    public function getPersonalTitle()
    {
        return $this->getAttribute($this->schema->personalTitle(), 0);
    }
    /**
     * Retrieves the primary group of the current user.
     *
     * @return Model|bool
     */
    public function getPrimaryGroup()
    {
        $groupSid = preg_replace('/\d+$/', $this->getPrimaryGroupId(), $this->getSid());

        return $this->query->newInstance()->findBySid($groupSid);
    }
}
