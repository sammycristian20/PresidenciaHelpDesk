<?php

namespace App\Http\Middleware;

use App\Http\Middleware\ActionPolicies;
use Illuminate\Contracts\Auth\Guard;
use Closure;
use App\User;
/**
 * 
 */
class AccessAccountPolicy extends ActionPolicies
{
	/**
     * The Guard implementation.
     *
     * @var Guard
     */
	protected $auth;

	/**
     * Create a new filter instance.
     *
     * @param Guard $auth
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

	/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
    	$role = (User::where('id',$request->segments()[3])->value('role') == 'user' ) ? 'user' : 'agent';
    	$this->routePermissions = $this->getRoutePermission($role);
        return parent::handle($request, $next);
    }

    /**
     * Method to return array containing route and corresponding permissions
     * @return array
     */
    private function getRoutePermission(string $role):array
    {
        return [
            'POST' => [
                '/api/account/restore/*'    => "{$role}_activation_deactivaton",
                '/api/account/deactivate/*'   => "{$role}_activation_deactivaton",
            ],
            'DELETE' =>[
            	'/api/account/delete/*'		=> "delete_{$role}_account"
            ]
        ];
    }
}