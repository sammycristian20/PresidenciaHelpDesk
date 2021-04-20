<?php

namespace App\Plugins\CustomJs\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\CustomJs\Model\CustomJs;

class HandleEventsController extends Controller
{
    /**
     * Function which handles the events fired from layout files and renders
     * corrosponding custom script saved by admins
     * @param Array  $request  payload data passed during firing event
     */
    public function handle(Array $request)
    {
        $this->renderScripts($this->getScript($request['fired_at']), $request['request']);
    }

    /**
     * Function fetches scripts from database with matching criteria.
     * It checkes script must be active for the given layout and if current
     * route matched parameter URL or not.
     * @var     RouteCollection  $routes
     * @var     String           $url      route matching current request url
     * @param   String           $firedAt  layout where script will be rendered
     * @return  Array                      Array containg script code as string
     */
    private function getScript($firedAt)
    {
        $routes = \Route::getRoutes();
        $url = [$routes->match(request())->uri()];
        array_push($url, '');
        return CustomJs::where([['fired_at', $firedAt], ['fire', '!=', 0]])->whereIn('parameter', $url)->pluck('script')->toArray();
    }

    /**
     * Function to echo custom script
     * @param  Array    $scripts  Array containg script code as string
     * @param  Request  $request  current request
     */
    private function renderScripts(Array $scripts, $request)
    {
        echo $this->supplyResourceIdVarible($request);
        foreach ($scripts as $script) {
            echo $script;
        }
    }

    /**
     * Function assigns first numeric value from request segments to javascript variable
     * named as $resourceId
     * @param  Request  $request
     */
    private function supplyResourceIdVarible($request)
    {
        $resourceId = 0;
        //fetch first numeric value from the segment and assign it to resourceId
        foreach ($request->segments() as $value) {
            if(is_numeric($value)) {
                $resourceId = $value;
                break;
            }
        }
        echo '<script type="text/javascript">var $resourceId = '.$resourceId.';</script>';
    }

    /**
     *=================================================================
     *  All the methods below this block are deprecated and will be
     *  removed in next release after rolling out the update for CustomJs
     *  plugin 
     *=================================================================
     */

    /**
     * @deprecated
     *
     */
    public function timelineScript($request)
    {
        if (array_key_exists(3, $request->segments())) {
            echo $this->customFieldValueArray($request->segments());
        }
        $this->renderScripts($this->getScript('timeline', $request->segments()));
    }
    /**
     * @deprecated
     *
     */
    public function createTicketScript($request)
    {
        if (array_key_exists(2, $request->segments())) {
            echo $this->customFieldValueArray($request->segments());
        }
        $this->renderScripts($this->getScript('createticket', $request->segments()));
    }
    /**
     * @deprecated
     *
     */
    public function buttonScript($request)
    {
        $this->renderScripts($this->getScript('button', $request->segments()));
    }
    /**
     * @deprecated
     *
     */
    public function agentScript($request)
    {
        $this->renderScripts($this->getScript('agentlayout', $request->segments()));
    }
    /**
     * @deprecated
     *
     */
    public function clientScript($request)
    {
        $this->renderScripts($this->getScript('clientlayout', $request->segments()));
    }
    /**
     * @deprecated
     *
     */
    public function adminScript($request)
    {
        $this->renderScripts($this->getScript('adminlayout', $request->segments()));
    }

    /**
     * @deprecated
     *
     */
    public function customFieldValueArray($parameters)
    {
        $lastElement = count($parameters);
        return '<script type="text/javascript">'
            . 'var $custom_field = {}
            $custom_field["' . $parameters[$lastElement - 2] . '"] = "' . $parameters[$lastElement - 1] . '";
            </script>';
    }
}