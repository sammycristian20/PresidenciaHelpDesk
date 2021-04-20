<?php

namespace App\Plugins\Ldap\Model;

use Illuminate\Database\Eloquent\Model;

class LdapAdAttribute extends Model
{
    protected $table = 'ldap_ad_attributes';

    protected $fillable = [

    /**
     * represents field in faveo database
     */
    'name',

    /**
     * If the attribute is added by default
     */
    'is_default',

    /**
     * If the attribute can be used to log-in
     */
    'is_loginable',

      /**
       * Ldap settings to which this attribute belongs
       */
      'ldap_id',
    ];
}
