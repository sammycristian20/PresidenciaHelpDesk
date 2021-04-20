<?php
namespace App\Model\helpdesk\Form;

use App\Repositories\FormRepository;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Observable;
use App\Model\helpdesk\Ticket\TicketAction;
use App\Model\helpdesk\Ticket\TicketRule;
use App\Model\helpdesk\TicketRecur\RecureContent as TicketRecur;
use App;
use App\Model\helpdesk\Ticket\TicketFilterMeta;
use App\FaveoReport\Models\ReportColumn;
use Event;
use Illuminate\Database\Eloquent\Builder;

/**
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 * Class FormField
 * @package App\Model\helpdesk\Form
 */
class FormField extends Model
{
    use Observable {
        boot as traitBoot;
    }

    protected $table = 'form_fields';

    protected $hidden = ['category_id','category_type','option_id','created_at','updated_at', 'is_active'];

    protected $appends = ['form_identifier'];

    protected $fillable = [
        'category_id', // id for category for eg. user form, ticket form, organisation form OR department OR helptopic
        'category_type', // type for category
        'sort_order', //order in which fields should be displayed
        'title',
        'type',
        'required_for_agent',
        'required_for_user',
        'display_for_agent',
        'display_for_user',
        'default',
        'is_linked',
        'media_option',
        'api_info',
        'pattern',
        'option_id',//it will be null if it is not refering to formFieldOption model.. i.e while querying
        //for the first time(no recursion) only those records should be fetched whose option_id is null
        //it has many options but it can also belongs to an option for nested fields

        'is_edit_visible',
        'is_active',
        'is_locked',
        'is_agent_config',
        'is_user_config',
        'unique',
        'form_group_id',
        'is_deletable',
        'is_customizable',
        'is_observable',
        'is_filterable',
        'is_user_config'
    ];

    protected static function boot()
    {
        self::traitBoot();
        self::setBaseFormQuery();
    }

    /**
     * Sets base query for form model.
     * PURPOSE:
     *  - all select attributes can be directly controlled from one place based on mode
     *  - based on scenario, we can skip a field. For eg. in edit ticket, files are not required
     *  - based on category we can skip a field. For eg, in case of automator(workflow, listener and SLAs), only those fields
     *      are required which are marked as observable
     *
     * REASON FOR NOT DOING THE TRADITIONAL WAY: logic for querying child fields are common but select/where statement in that based on
     *  category, scenario and panel can end up into writing too much code
     */
    private static function setBaseFormQuery()
    {
        static::addGlobalScope(function (Builder $builder) {
            $formRepository = FormRepository::getInstance();

            if ($formRepository->getMode() == 'render') {
                $builder->select(
                    'id',
                    'category_id',
                    'category_type',
                    'type',
                    'api_info',
                    'pattern',
                    'option_id',
                    'unique',
                    'sort_order',
                    'form_group_id',
                    'default',
                    'media_option'
                );

                if ($formRepository->getPanel() !== 'client' && !$formRepository::isAutomatorMode()) {
                    $builder->where('display_for_agent', 1)->addSelect('required_for_agent as required');
                }

                if ($formRepository->getPanel() === 'client') {
                    $builder->where('display_for_user', 1)->addSelect('required_for_user as required');
                }

                if ($formRepository->getScenario() === 'recur') {
                    $builder->where('type', '!=', 'file');
                }

                if (in_array($formRepository->getScenario(), ['edit', 'fork'])) {
                    // Form fields which are custom fields and attachments, should not come in edit
                    $builder->where('is_edit_visible', 1)->where(\DB::raw("CONCAT(`default`,'_',`type`)"), '!=', '0_file');
                }

                if($formRepository->getScenario() === 'fork') {
                    /**
                     * In edit form assigned field is not required but assigned might be useful while
                     * forking as user may decide to select a new agent on the forked ticket.
                     */
                    $builder->orWhere('unique', 'assigned_id');
                }

                if ($formRepository::isAutomatorMode()) {
                    $builder->where('is_observable', 1)->where('type', '!=', 'file');

                    if ($formRepository->getScenario() === 'actions') {
                        $builder->whereNotIn('title', ['Subject', 'Description']);
                    }
                }
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setHiddenFields();
    }

    /**
     * Sets hidden fields based on form mode
     */
    private function setHiddenFields()
    {
        $formRepository = FormRepository::getInstance();
        // setting column visibility based on mode
        if ($formRepository->getMode() == 'render') {
            $this->hidden = array_merge($this->hidden, ['form_group_id', 'sort_order']);

            if ($formRepository::isAutomatorMode()) {
                $this->hidden[] = 'description';
            }

            $this->appends = ['label', 'description', 'validation_message'];
        }
    }

    /**
     * This identifier will be used at frontend to know if it is a form_field, form field option, help topic option, department option or label
     * @return string
     */
    public function getFormIdentifierAttribute()
    {
        return "form_field_".$this->id;
    }

    public function options()
    {
        return $this->hasMany('App\Model\helpdesk\Form\FormFieldOption', 'form_field_id', 'id');
    }

    public function labelsForFormField()
    {
        return $this->labels()->where('meant_for', 'form_field');
    }

    public function labelsForValidation()
    {
        return $this->labels()->where('meant_for', 'validation');
    }

    /**
     * Morph to multiple models like category/department/helptopic
     */
    public function category()
    {
        return $this->morphTo();
    }

    /**
     * value in custom_form_value table
     */
    public function values()
    {
        return $this->hasMany('App\Model\helpdesk\Form\CustomFormValue', 'form_field_id');
    }

    /**
     * using polymorphic relation for binding
     */
    public function labels()
    {
        $lang = App::getLocale();

        // adding an orderBy, so that the label with current language can be at top
        return $this->morphMany('App\Model\helpdesk\Form\FormFieldLabel', 'labelable')
            ->select("*", DB::raw("(CASE when language='$lang' THEN 1 ELSE 0 END) as is_current_language"))
            ->orderBy("is_current_language", "desc")
            ->orderBy("id", "asc");
    }

    public function beforeDelete($model)
    {
        //deleting one by one will make sure that it fires delete event in the child model
        foreach ($this->options as $option) {
            $option->delete();
        }

        foreach ($model->labels as $label) {
            $label->delete();
        }
        Event::dispatch('delete-extra-entries', [$this->id]);
        //delete all custom form value also
        $model->values()->delete();

        // deleting the same in actions, rules and recur table
        TicketAction::where('field', "custom_$this->id")->delete();
        TicketRule::where('field', "custom_$this->id")->delete();
        TicketRecur::where('option', "custom_$this->id")->delete();

        // deleting associated filters
        TicketFilterMeta::where('key', "custom_$this->id")->delete();
        ReportColumn::where('key', "custom_$this->id")->delete();
    }

    public function getLabelAttribute()
    {
        return $this->labelsForFormField()->value("label");
    }

    public function getValidationMessageAttribute()
    {
        return $this->labelsForValidation()->value("label");
    }

    public function getDescriptionAttribute()
    {
        return $this->labelsForFormField()->value("description");
    }

    /**
     * relationship with form group and form field
     */
    public function formGroup()
    {
        return $this->belongsTo(FormGroup::class, 'form_group_id');
    }

    /**
     * Gets form field label by its identifier
     * @param string $identifier
     * @return string
     */
    public static function getLabelByIdentifier(string $identifier)
    {
        // gives label by custom_ format
        $formFieldId = str_replace('custom_', '', $identifier);
        $formField = FormField::where('id', $formFieldId)->first();
        if ($formField && $formField->label) {
            return $formField->label;
        }
        return '';
    }

    public function getTypeAttribute($value)
    {
        $formRepository = FormRepository::getInstance();
        
        if ($formRepository->getPanel() === 'client' && $this->unique === 'requester') {
            return 'client-panel-requester';
        }

        if ($formRepository::isAutomatorMode() && $value === 'htmltextarea') {
            return 'textarea';
        }

        if ($formRepository::isAutomatorMode() && !in_array($value, ['api', 'radio', 'checkbox', 'number', 'select', 'text', 'date', 'multiselect'])) {
            return 'text';
        }

        return $value;
    }

    public function getUniqueAttribute($value)
    {
        return $value ? : "custom_".$this->id;
    }
}
