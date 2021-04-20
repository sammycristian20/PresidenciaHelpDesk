<?php

namespace App\Model\Common;

use App\BaseModel;

class TemplateShortCode extends BaseModel
{
    protected $table = 'template_shortcodes';
    protected $fillable = [
   		/**
   		 * name of the key which will be passed in $variable array to replace with shortcodes
   		 */
    	'key_name',

    	/**
    	 * name of shortcode to display and save in templates settings so that users can identify,
    	 * add/update the shortcode in template content
    	 * Format for shortcode: {!! $your_short_code_name !!}
    	 */
    	'shortcode',

    	/**
    	 * key available in the language file which contains the description content of the shortcode
    	 * reason for saving the key in db is to be able to add new shortcode and it's description
    	 * in such way that we do not have to worry to add translation for each shortcode in TemplateVariablesController
    	 * Format: 'lang.shortcode_your_short_code_name_description'
    	 */
    	'description_lang_key',

        /**
         * If a shortcode is specific for plugin's email templates then we must specify the name of
         * plugin or module which uses that short code in its email templates. It will help to show/hide
         * shortcodes in the template edit page accodring to plugin/module status. Also it will help future
         * devs to understand from where shortcodes are seeded and ditinguish between defualt and plugin's
         * shortcodes.
         * eg: task or billing plugin uses some shortcodes which are used by themselves. 
         */
        'plugin_name'
    ];
}
