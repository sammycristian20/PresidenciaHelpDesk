<?php

namespace App\Model\helpdesk\Theme;

use App\Facades\Attach;
use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $table = 'system_portal';
    protected $fillable = ['id', 'admin_header_color', 'agent_header_color', 'client_header_color', 'client_button_color', 'client_button_border_color', 'client_input_field_color', 'logo', 'icon', 'created_at', 'updated_at','logo_icon_driver', 'is_enabled_breadcrumb'];

    public function getIconAttribute($value)
    {
        $icon = $value ?: assetLink('image', 'favicon');

        $whiteLabelCondition = \Event::dispatch('helpdesk.apply.whitelabel');

        /*If white label folder is present and admin not upload any custom favicon that time
        default white label favicon will appear
        */

        if ($whiteLabelCondition && !$value) {
            $icon = assetLink('image', 'whitefavicon');
        }

        return $icon;
    }

    public function getLogoAttribute($value)
    {
        $logo = $value ?: assetLink('image', 'logo');

        $whiteLabelCondition = \Event::dispatch('helpdesk.apply.whitelabel');

        /*If white label folder is present and admin not upload any custom logo that time
        default white label logo will appear
        */
        
        if ($whiteLabelCondition && !$value) {
            $logo = assetLink('image', 'whitelabel');
        }

        return $logo;
    }
}
