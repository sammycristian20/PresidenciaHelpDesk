<?php

namespace App\Http\Controllers\Admin\helpdesk;

use App\Http\Controllers\Controller;
use App\Model\helpdesk\Theme\Widgets;
use Illuminate\Http\Request;
use Validator;

/**
 * WidgetController
 * This controller is used for updating Widgets only accessible by admin.
 *
 * @author      Manish <manish.verma@ladybirdweb.com>
 */
class WidgetController extends Controller {
    /**
     * forcing admin role middleware on the methods of this controller
     */
    public function __construct() {
        $this->middleware('role.admin');
    }

    /**
     * Function to return the widget list page view
     * For footer it returns themes.default1.admin.helpdesk.theme.widget.blad.php (default)
     * for social-icon it return themes.default1.admin.helpdesk.theme.social.blade.php
     * 
     * @param   string  $type
     * @return  \Illuminate\Http\Response  HTML view
     */
    public function getWidgetView(string $type=null)
    {
        $viewToReturn = ($type=="social-icon")?"social":"widgets";
        return view('themes.default1.admin.helpdesk.theme.'.$viewToReturn);
    }

    /**
     * Function to return widget list data
     * 
     * @param  Request  $request
     * @param  string   $type    slug foorter/social-icon/null
     * @return \Illuminate\Http\JsonResponse json response 
     */
    public function getWidgetList(Request $request, string $type = null)
    {
        $baseQuery = Widgets::when($type, function($query) use($type){
            $query->where('type', $type);
        });
        $limit = (int)$request->input('limit')?:10;
        $searchString = $request->input('search-query', '');
        $sortOrder = $request->input('sort-order') == 'desc' ? 'desc' : 'asc';
        $sortField = $request->input('sort-field', 'name');
        $result = $baseQuery->where(function($query) use($searchString){
            $query->where("name", "LIKE", "%$searchString%")
            ->orWhere("title", "LIKE", "%$searchString%")
            ->orWhere("value", "LIKE", "%$searchString%");
        })->orderBy($sortField, $sortOrder)->paginate($limit)->toArray();
        $result['widgets'] = $result['data'];
        unset($result['data']);
        return successResponse('', $result);
    }

    /**
     * Function to update widget list record
     * 
     * @param  Request  $request
     * @param  Widgets  $widget    widget record to update
     * @return \Illuminate\Http\JsonResponse json response 
     */
    public function updateWidget(Request $request, Widgets $widget)
    {
        if($widget->type == 'social-icon') {
            $v = Validator::make($request->all(), [
                'value' => 'url'
            ]);
            if ($v->fails()) {
                return errorResponse(['value' => $v->errors()->first()], FAVEO_VALIDATION_ERROR_CODE);
            }
        }
        $widget->update($request->only(['title', 'value']));
        return successResponse(trans('lang.updated_successfully'));
    }
}
