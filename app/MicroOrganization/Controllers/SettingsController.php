<?php

namespace App\MicroOrganization\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Ticket\Ticket_Status;
// use App\Bill\Requests\BillRequest;
use Lang;
/**
 * Setting for the bill module
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
                            <a href="' . url('helpdesk/micro-organizarion') . '">
                                <span class="fa-stack fa-2x">
                                    <i class="fa fa-building-o"></i>
                                </span>
                            </a>
                        </div>
                        <p class="box-title" >'.Lang::get('lang.micro_organization').'</p>
                    </div>
                </div>';
    }
   

    
}
