<?php

namespace App\Plugins\Ldap\Model;

use App\Plugins\Ldap\Controllers\LdapConnector;
use App\Traits\Observable;
use App\Traits\UserImport;
use Illuminate\Database\Eloquent\Model;
use Crypt;

class Ldap extends Model
{
    use Observable;

    protected $table = 'ldap';

    protected $fillable = [

        /**
         * Directory Service Schema
         */
        'schema',

        /**
         * domain of ldap server
         */
        'domain',

        /**
         * admin username of ldap server
         */
        'username',

        /**
         * admin password of ldap server
         */
        'password',

        /**
         * SSL or TLS
         */
        'encryption',

        /**
         * Port number on which LDAP connection has to be made
         */
        'port',

        /**
         * if ldap credentials are valid or not
         */
        'is_valid',

        /**
         * Forgot password link for ldap
         */
        'forgot_password_link',

        /**
         * Label for ldap login button
         */
        'ldap_label',

        /**
         * Username prefix
         */
        'prefix',

        /**
         * Username suffix
         */
        'suffix',
    ];

    protected $hidden = ['password'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encrypt($value);
    }

    public function getPasswordAttribute($value)
    {
        if ($value) {
            return Crypt::decrypt($value);
        }
    }

    /**
     * relation for getting search basis
     */
    public function searchBases()
    {
        return $this->hasMany('App\Plugins\Ldap\Model\LdapSearchBase');
    }

    public function adAttributes()
    {
        return $this->hasMany(LdapAdAttribute::class);
    }

    public function faveoAttributes()
    {
        return $this->hasMany(LdapFaveoAttribute::class);
    }

    public function afterCreate($model)
    {

        $this->seedAdAttributes($model->id);

        $defaultAdAttributeId = LdapAdAttribute::where('ldap_id', $model->id)->where('name', 'FAVEO DEFAULT')->value('id');

        $defaultUserImportAttributes = array_merge(UserImport::$userAttributes, UserImport::$userProperties);

        foreach ($defaultUserImportAttributes as $defaultUserImportAttribute) {
            $ldapFaveoAttribute = (new LdapFaveoAttribute)->fill(['name'=> $defaultUserImportAttribute, 'ldap_id'=> $model->id,
                'overwrite'=>false, 'overwriteable'=>true, 'editable'=>true, 'mapped_to'=>$defaultAdAttributeId]);

            if ($defaultUserImportAttribute == 'import_identifier') {
                $ldapFaveoAttribute->overwriteable = false;
            }

            $ldapFaveoAttribute->save();
        }
    }


    /**
     * Seeds Ad Attributes for a particular Ldap instance
     * @param  int $ldapId 
     * @return null
     */
    private function seedAdAttributes($ldapId)
    {
        foreach (LdapConnector::$AD_ATTRIBUTES as $defaultAdAttribute) {
            LdapAdAttribute::create(['name'=> $defaultAdAttribute, 'ldap_id'=> $ldapId]);
        }


        foreach (LdapConnector::$LOGINABLE_AD_ATTRIBUTES as $defaultAdAttribute) {
            LdapAdAttribute::create(['name'=> $defaultAdAttribute, 'ldap_id'=> $ldapId, 'is_loginable'=>1]);
        }
    }

    public function beforeDelete($model)
    {
        foreach ($model->searchBases as $searchBase) {
            $searchBase->delete();
        }

        foreach ($model->adAttributes as $adAttribute) {
            $adAttribute->delete();
        }

        foreach ($model->faveoAttributes as $faveoAttribute) {
            $faveoAttribute->delete();
        }
    }

    public function setPortAttribute($value)
    {
        $this->attributes['port'] = $value ?: null;
    }
}
