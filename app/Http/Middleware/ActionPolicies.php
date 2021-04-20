<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Base Middleware class to check if the user has permission to perform
 * the action processed by the request. Eg: check if user can change ticket
 * status, can reply on ticket etc based on permission set on user's profile.
 * 
 * It handles the request and check if the Auth user has permission to perform
 * the action depicted by permissions.
 *
 * NOTE: This class currently simply handles resource action check based on URL
 * and does not consider request data. So it can not handle the permission check
 * where permission will be based on data posted in the request. Eg: Deleting a
 * ticket is simply status change request as ticket/change-status/{id}/{ticketIds}
 * So using this class we can check is user can change status of the ticket but can
 * not check if user can change ticket status to delete or other specific status.
 *
 * @var string $policyClass      Class name with complete namespace which handles
 * permission check
 * @var string $policyMethod     name of the method defined in $policyClass which
 * actually check permission
 * @var array  $routePermissions Array containing routes and their corrosponding
 * permissions
 *
 * Usage:
 * 1. Extend this class in your custom policy middleware class
 * 2. Override $policyClass with the namespace of the policy class to use for
 * permission checks
 * 3. Override $policyMethod with the name of method which checks permission
 * 4. Override $routePermissions to store route permissions as below format and
 * replace integer variable resource id to *
 * ['RouteMethodName' => ['route-string' => 'permission to check',..],..]
 * eg: 
 * route to delete asset 1 : service-desk/api/asset-delete/1 will be stored as
 * ['DELETE' => '/service-desk/api/asset-delete/*'    => 'delete_asset']]
 * 5. call handle method of this class from your custom policy middleware class
 * eg: see App\Plugins\ServiceDesk\Middleware\SdAccessPolicy
 *
 * @author Manish Verma<manish.verma@ladybirdweb.com>
 * @since v4.0.0
 *
 * @todo expand this class functionality to handle
 * - alphanumeric string variables in route
 * - handle the scenario explained for checking status change to delete
 * - handle the permission check based on posted request data
 * - 
 */
class ActionPolicies
{
    /**
     * @var string
     */
    protected $policyClass = 'App\User';

    /**
     * @var string
     */
    protected $policyMethod = 'has';

    /**
     * @var array
     */
    protected $routePermissions = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
        try {
            $callingURL = preg_replace('(\d+)', '*', str_replace($request->root(), '', $request->url()));
            $permission = $this->routePermissions[$request->method()][$callingURL];
            $method = $this->policyMethod;
            if(!$this->policyClass::$method($permission)) {
                if($request->ajax() + $request->wantsJson() + (stripos($request->url(), 'api')!== false)) {

                    return errorResponse(trans('lang.permission_denied'), 401);
                }

                return redirect()->back()->with('fails', trans('lang.permission_denied'));
            }

            return $next($request);
        } catch(\Exception $e) {

            return $next($request);
        }
    }
}
