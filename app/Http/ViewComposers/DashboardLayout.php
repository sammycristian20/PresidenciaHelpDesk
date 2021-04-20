<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

use App\Model\helpdesk\Settings\CommonSettings;

class DashboardLayout {

    public function __construct() {
        
    }

    public function compose(View $view) {
        $settings = CommonSettings::where('option_name', '=', 'dashboard-statistics')->select('option_value')->first()->toArray();
        $dashboard_statistics = [];
        if(count($settings) > 0) {
            $dashboard_statistics = explode(',', $settings['option_value']);
        }
        $view->with([
            'dashboard_statistics' => $dashboard_statistics,
        ]);
    }

}
