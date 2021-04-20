<?php

namespace App\Plugins\Ldap\Model;

use App\Model\helpdesk\Form\FormField;
use Illuminate\Database\Eloquent\Model;
use Lang;

class LdapFaveoAttribute extends Model
{
    protected $table = 'ldap_faveo_attributes';

    protected $fillable = [

        /**
         * represents field in faveo database
         */
        'name',

        /**
         * overwrites on ldap user import
         */
        'overwrite',

        /**
         * Id of the ldap attribute
         */
        'mapped_to',

        /**
         * If field is editable
         */
        'editable',

        /**
         * if a field is allowed change overwrite functionality
         */
        'overwriteable',

        /**
         * Parent Ldap setting
         */
        'ldap_id',
    ];

    public $appends = ['label', 'description'];

    public function adAttribute()
    {
        return $this->belongsTo('App\Plugins\Ldap\Model\LdapAdAttribute', 'mapped_to');
    }

    public function getLabelAttribute()
    {
        if (strpos($this->name, 'custom_') === false) {
            return Lang::get("Ldap::lang.$this->name");
        }

        // otherwise look for custom field label
        return FormField::getLabelByIdentifier($this->name);
    }

    public function getDescriptionAttribute()
    {
        if (strpos($this->name, 'custom_') === false) {
            return Lang::get("Ldap::lang.".$this->name."_description");
        }
        return Lang::get("Ldap::lang.user_import_custom_field_description");
    }
}
