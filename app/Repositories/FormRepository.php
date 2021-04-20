<?php


namespace App\Repositories;

use App\Model\helpdesk\Agent\Department;
use App\Model\helpdesk\Form\FormCategory;
use App\Model\helpdesk\Form\FormField;
use App\Model\helpdesk\Form\FormFieldOption;
use App\Model\helpdesk\Form\FormGroup;
use App\Model\helpdesk\Manage\Help_topic;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Lang;

class FormRepository
{
    private static $classObject;

    private function __construct()
    {
        # constructor is private so that it cannot be instantiated directly from outside
    }

    /**
     * Get instance of the class
     * @return FormRepository
     */
    public static function getInstance()
    {
        self::$classObject === null && self::$classObject = new FormRepository();
        return self::$classObject;
    }

    public static function destroyInstance()
    {
        self::$classObject = null;
    }

    /**
     * Category of the form
     * @var string
     */
    private $category = 'ticket';

    /**
     * If it is used for configuring forms or rendering it. Possible values `config`, `render`
     * @var string
     */
    private $mode = 'config';

    /**
     * Panel from which form is requested. possible values agent, admin, user
     * @var string
     */
    private $panel = 'agent';

    /**
     * Represent the process which is trying to access form. For eg. create, edit, actions, rules
     * @var string
     */
    private $scenario = 'create';

    /**
     * Sets mode of the form
     * @param string $category
     */
    public function setCategory($category = 'ticket')
    {
        $this->category = $category;
    }

    /**
     * Sets mode of the form
     * @param string $mode
     */
    public function setMode($mode = 'config')
    {
        $this->mode = $mode;
    }

    /**
     * Sets panel from which form is accessed
     * @param string $panel
     */
    public function setPanel($panel = 'agent')
    {
        $this->panel = $panel;
    }

    /**
     * Sets panel from which form is accessed
     * @param string $scenario
     */
    public function setScenario($scenario = 'create')
    {
        $this->scenario = $scenario;
    }

    /**
     * Gets panel from which it is requested
     * @return mixed|string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Gets mode of the form
     * @return mixed|string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Gets panel from which it is requested
     * @return mixed|string
     */
    public function getPanel()
    {
        return $this->panel;
    }

    /**
     * Gets panel from which it is requested
     * @return mixed|string
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * If form is in automator mode
     * @return bool
     */
    public static function isAutomatorMode()
    {
        return in_array(self::getInstance()->getCategory(), ['workflow', 'listener', 'sla']);
    }

    /**
     * Gets all formFields by parent (including labels and options)
     * @param  QueryBuilder  $parent who has either nodes or formFields relation AND formGroups relation
     * @return object
     */
    public function getFormQueryByParentQuery($parent)
    {
        // In case of Helptopic and Department, the relation is named as `nodes` but in
        // case of FormGroup and FormCategory, it is named as formFields
        $relationName = method_exists($parent->getModel(), 'nodes') ? 'nodes' : 'formFields';

        if ($this->getMode() != 'render') {
            $parent->with(["$relationName.labelsForFormField", "$relationName.labelsForValidation"]);
        }

        $baseQuery = $parent->with([
            "$relationName" => function ($query) {
                $query->where('is_active', 1);
                return $this->queryBuilderForNestedFormFields($query);
            }
        ]);

        $doesFormGroupExist = method_exists($parent->getModel(), 'formGroups');

        if ($doesFormGroupExist) {
            $baseQuery->with([
                'formGroups'=> function ($q) {
                    // if not group format, we will query for form group fields, which is later gets converted into form render format
                    if (self::getMode() == 'render') {
                        $this->getFormQueryByParentQuery($q);
                    }
                }
            ]);
        }

        return $baseQuery;
    }

    /**
     * It builds a query based on how deep the nesting is. If it finds no nesting in next layer it returns the result
     * else it loops over itself again
     * It is a helper method for 'getFormFieldsByCategory' and should not be used by any other method
     * @param $query
     * @return object   query for having n-layer of nested fields
     */
    public function queryBuilderForNestedFormFields(&$query)
    {
        $query = $query->where('is_active', 1);
        if ($query->with('options')->count()) {
            if ($this->getMode() != 'render') {
                $query = $query->with('options.labels', 'options.nodes.labelsForFormField', 'options.nodes.labelsForValidation');
            }

            $query = $query->orderBy('sort_order', 'ASC')->with([
                'options'=> function ($q1) {
                    $q1->orderBy('sort_order', 'ASC');
                },
                'options.nodes' => function ($q) {
                    // todo: no file mode will be decided based on the scenario
                    // $noFileMode && $q->where('type', '!=', 'file');

                    $this->queryBuilderForNestedFormFields($q);
                },
                'options.formGroups'
            ]);
        }
        return $query;
    }

    /**
     * gets all ticket related custom fields including child fields of helptopic and department along
     * with their current label without any meta data
     * NOTE: Attachment fields will not be returning, because it is not required in any of the cases
     * that we are using yet.
     * @return Collection
     */
    public static function getTicketCustomFieldList() : Collection
    {
        self::getInstance()->setCategory('ticket');
        self::getInstance()->setMode('render');
        self::getInstance()->setPanel('admin');

        $customFields = FormField::where(function ($q) {
            $q->where('category_type', 'App\Model\helpdesk\Manage\Help_topic')
                ->orWhere('category_type', 'App\Model\helpdesk\Agent\Department')
                ->orWhere(function ($q1) {
                    $q1->where('category_type', 'App\Model\helpdesk\Form\FormCategory')
                        // category 1 means ticket
                        ->where('category_id', 1);
                });
        })->where('default', 0)
            ->where('is_active', 1)
            ->where('is_filterable', 1)
            ->select('id')->where('type', '!=', 'file')->get();

        //getting custom fields for groups also
        $customFields = $customFields->merge(self::getGroupFormFieldList());

        // get all options that belong to ticket, now those options form field ids should be taken,
        // now those form field can also have options, now those options should also be considered.
        // This should go ON and ON until none of the option is remaining
        self::appendRecursiveFormFields($customFields);

        // since all form fields which belongs to a can be linked to a ticket at any given time,
        // so we can consider it to be a normal ticket form field

        return $customFields;
    }

    /**
     * Gets user custom field list
     * @return Collection
     */
    public static function getUserCustomFieldList() : Collection
    {
        self::getInstance()->setCategory('user');
        self::getInstance()->setMode('render');
        self::getInstance()->setPanel('admin');

        return FormCategory::where('category', 'user')->first()->formFields()
            ->where('default', 0)
            ->where('is_active', 1)
            ->where('is_filterable', 1)
            ->select('id')
            ->where('type', '!=', 'file')
            ->get();
    }


    /**
     * Appends nested form field which doesn't belong to any category but options
     * @param Collection &$formFields
     * @param Collection|null $additionFormFields
     * @param bool $isIterating
     * @return null
     */
    public static function appendRecursiveFormFields(
        Collection &$formFields,
        Collection $additionFormFields = null,
        bool $isIterating = false
    ) {
        $fieldsToIterate = $isIterating ? $additionFormFields : $formFields;

        $formFieldIds = $fieldsToIterate->map(function ($formField) {
            return $formField->id;
        });

        // query into option table for formFields,
        $formFieldOption = FormFieldOption::whereIn('form_field_id', $formFieldIds)->pluck('id')->toArray();

        $additionFormFields = FormField::whereIn('option_id', $formFieldOption)->where('type', '!=', 'file')
            ->select('id')->get();

        // if no additional form field is found, abort the recursion
        if (!$additionFormFields->count()) {
            return;
        }

        $formFields = $formFields->merge($additionFormFields);

        self::appendRecursiveFormFields($formFields, $additionFormFields, true);
    }

    /**
     * Gets all form fields which are associated with groups and not directly to ticket/user/organisation
     * @return Collection
     */
    private static function getGroupFormFieldList() : Collection
    {
        return FormField::whereHas('formGroup', function ($subQuery) {
            $subQuery->whereGroupType('ticket');
        })->where('type', '!=', 'file')->get(['id']);
    }

    /**
     * Formats form element by merging form group and form fields at same level and sorting them
     * @param FormCategory|FormGroup|Help_topic|Department|FormFieldOption $formCategory
     * @param bool $isGroupFormat
     * @return void
     */
    public static function formatFormElements(&$formCategory, bool $isGroupFormat = false) : void
    {
        // for FormGroup there won't be any child groups, so to avoid code break,
        // we initialise it with empty collection
        $formGroups = $formCategory->formGroups;
        $formGroups = $formGroups ?: new Collection;

        // In case of Helptopic and Department, the relation is named as `nodes` but in
        // case of FormGroup and FormCategory, it is named as formFields
        $relationName = method_exists($formCategory, 'nodes') ? 'nodes' : 'formFields';

        $formFields = $formCategory->$relationName;

        (new self)->formatFormGroup($formGroups, $isGroupFormat);

        foreach ($formFields as &$formField) {
            $formOptions = $formField->options;
            self::formatNestedFormElements($formOptions, $isGroupFormat);
        }
        // go into form fields and go through all nodes and do the same sorting
        // and getting there values so that indexes can be recalculated
        unset($formCategory->$relationName, $formCategory->formGroups);

        $formCategory->$relationName = $formFields->concat($formGroups)->sortBy('sort_order')->values();
    }

    /**
     * Formats nested child elements by merging groups and nodes into nodes,
     * so that even group can be seen as an element of node.
     * Since, child fields has a different key than parent (parent -> form-fields, child->nodes),
     * this method is seperated
     * @param Collection $formOptions
     * @param bool $isGroupFormat
     * @return void
     * @throws \Exception
     */
    public static function formatNestedFormElements(Collection &$formOptions, bool $isGroupFormat = false) : void
    {
        // loop over all options
        // in each options, there will be nodes and formGroups, which has to be
        // merged and returned
        foreach ($formOptions as &$formOption) {
            // for further processing of the function, option instance must have `formGroups` and `nodes` properties
            if (!isset($formOption->formGroups) || !isset($formOption->nodes)) {
                throw new \Exception("formOptions must have formGroups and nodes as properties");
            }

            $childFormGroups = $formOption->formGroups;
            $childFormFields = $formOption->nodes;

            (new self)->formatFormGroup($childFormGroups, $isGroupFormat);

            foreach ($childFormFields as $childFormField) {
                $childFormFieldOptions = $childFormField->options;
                self::formatNestedFormElements($childFormFieldOptions, $isGroupFormat);
            }

            // removing nodes and formGroups properties so that these can be readded
            // with newly merged values of form fields
            unset($formOption->nodes, $formOption->formGroups);
            $formOption->nodes = $childFormFields->concat($childFormGroups)->sortBy('sort_order')->values();
        }
    }

    /**
     * Adds additional properties to form groups required in rendering the form
     * @param Collection &$formGroups
     * @param bool $isGroupFormat
     * @return void
     */
    private function formatFormGroup(Collection &$formGroups, bool $isGroupFormat) : void
    {
        $allGroupFormFields = new Collection();
        foreach ($formGroups as &$formGroup) {
            if ($isGroupFormat) {
                $formGroup->sort_order = $formGroup->pivot->sort_order;
                $formGroup->type = 'group';
                $formGroup->title = $formGroup->name;

                // form group blocks should be deletable
                $formGroup->is_deletable = 1;
                $formGroup->reference_type = $formGroup->pivot->getTable();
                $formGroup->reference_id = $formGroup->pivot->id;
            } else {
                $groupFormFields = $formGroup->formFields;

                $allGroupFormFields = $allGroupFormFields->merge($groupFormFields);

                foreach ($groupFormFields as $formField) {
                    // assumption that there won't be more than 1000 form fields in single form group
                    $formField->sort_order = $formGroup->pivot->sort_order.'.'. str_pad($formField->sort_order, 4, "0", STR_PAD_LEFT);
                }
            }
        }
        $formGroups = $isGroupFormat ? $formGroups : $allGroupFormFields;
    }

    /**
     * Adds additional actions to workflow/listener
     * @param Collection $formFields
     */
    public function addAutomatorAdditionalFormFields(Collection &$formFields)
    {
        if (self::getCategory() == 'workflow' && self::getScenario() == 'actions') {
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.reject_ticket'), 'unique'=>'reject_ticket', 'default'=> 1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.ticket_number_prefix') , 'required'=> true, 'type'=>'text', "pattern"=>"^[A-Za-z0-9]{3,8}$", 'unique'=>'ticket_number_prefix', 'validation_message'=> Lang::get('lang.only_eight_characters_are_allowed_in_ticket_number_prefix'), 'default'=>1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.tags'), 'type'=>'multiselect', 'unique'=>'tag_ids', 'api_info'=>'url:=/api/dependency/tags?paginate=true;;', 'default'=>1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.labels'), 'type'=>'multiselect', 'unique'=>'label_ids', 'api_info'=>'url:=/api/dependency/labels?paginate=true;;', 'default'=>1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.requester'), 'type'=>'api', 'unique'=>'user_id', 'api_info'=>'url:=/api/dependency/users;;', 'default'=>1]);
        }

        if (self::getCategory() == 'sla' && self::getScenario() == 'rules') {
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.tags'), 'type'=>'multiselect', 'unique'=>'tag_ids', 'api_info'=>'url:=/api/dependency/tags?paginate=true;;', 'default'=>1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.labels'), 'type'=>'multiselect', 'unique'=>'label_ids', 'api_info'=>'url:=/api/dependency/labels?paginate=true;;', 'default'=>1]);
        }

        if (self::getScenario() == 'actions') {
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.approval_workflow') , 'required'=> true, 'api_info'=>"url:=/api/dependency/approval-workflows?paginate=true;;", 'unique'=>'approval_workflow_id', 'type'=>'api', 'default'=>1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.team') , 'required'=> true, 'api_info'=>"url:=/api/dependency/teams?paginate=true;;", 'unique'=>'team_id', 'type'=>'api', 'default'=>1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.send_email_to_agent') , 'required'=> true, 'unique'=>'mail_agent', 'type'=>'custom', 'default'=>1]);
            $formFields->add((object)['id'=> uniqid(), 'label'=> Lang::get('lang.send_email_to_requester') , 'required'=> true, 'unique'=>'mail_requester', 'type'=> 'custom', 'default'=>1]);
        }

        \Event::dispatch('ticket-automator-form-dispatch', [&$formFields]);
    }
}
