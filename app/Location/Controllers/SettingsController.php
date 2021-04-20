<?php

namespace App\Location\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Ticket_Status;
// use App\Bill\Requests\BillRequest;
use Lang;
/**
 * Setting for the Location module
 * 
 * @abstract Controller
 * @author Ladybird Web Solution <admin@ladybirdweb.com>
 * @name SettingsController
 * 
 */

class SettingsController extends Controller {

    public function __construct() {
        //$this->middleware(['auth', 'roles']);
    }
    /**
     * 
     * get the setting icon on admin panel
     * 
     * @return string
     */
    public function settingsLink() {
        return ' <div class="col-md-2 col-sm-6">
                    <div class="settingiconblue">
                        <div class="settingdivblue">
                            <a href="' . url('helpdesk\location-types') . '">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-map fa-stack-1x"></i>
                                </span>
                            </a>
                        </div>
                        <div class="text-center text-sm">'.Lang::get('lang.location').'</div>
                    </div>
                </div>';
    }
   

    
}
