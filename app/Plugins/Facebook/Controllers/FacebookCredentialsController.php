<?php


namespace App\Plugins\Facebook\Controllers;


use App\Http\Controllers\Controller;
use App\Model\MailJob\Condition;
use App\Plugins\Facebook\Model\FacebookCredential;
use App\Plugins\Facebook\Requests\FacebookCredentialsRequest;
use Illuminate\Http\Request;

class FacebookCredentialsController extends Controller
{
    /**
     * Returns a list of facebook pages integrated with the system
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = FacebookCredential::query();
        $searchString = $request->input('search-query') ?: '';

        if ($searchString) {
            $query->where('page_id', 'LIKE',  "%$searchString%")
                ->orWhere('page_name', 'LIKE',  "%$searchString%");
        }

        $pages = $query->orderBy(
            $request->input('sort-field') ?: 'updated_at',
            $request->input('sort-order') ?: 'desc'
        )->paginate($request->input('limit') ?: 10)->toArray();

        $pages['pages'] = $pages['data'];
        unset($pages['data']);

        return successResponse('', $pages);
    }

    /**
     * Adds a facebook page to the system
     * @param FacebookCredentialsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FacebookCredentialsRequest $request)
    {
        $credentialsCreated = FacebookCredential::create(
            $request->only(['page_id','page_access_token','page_name','verify_token','new_ticket_interval'])
        );

        return ($credentialsCreated)
            ? successResponse(trans('Facebook::lang.facebook_integrated_successful'))
            : errorResponse(trans('Facebook::lang.facebook_integration_fail'));
    }

    /**
     * Destroys the facebook page
     * @param $integrationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($integrationId)
    {
        $integration = FacebookCredential::find($integrationId);
        if (!$integration) {
            return errorResponse(trans('Facebook::lang.facebook_no_such_page_found'));
        }
        return ($integration->delete())
            ? successResponse(trans('Facebook::lang.facebook_integration_deleted'))
            : errorResponse(trans('Facebook::lang.facebook_integration_delete_fail'));
    }

    /**
     * Returns the facebook webhook verify token
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVerifyToken()
    {
        if (FacebookCredential::count()) {
            $verifyToken = FacebookCredential::value('verify_token');
            return successResponse('', compact('verifyToken'));
        }
        return errorResponse(trans('Facebook::lang.facebook_verify_token_not_set'));
    }

    /**
     * Sets the facebook page status
     * @param $pageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus($pageId)
    {
        $integration = FacebookCredential::where('page_id', $pageId)->first();
        if (!$integration) {
            return errorResponse(trans('Facebook::lang.facebook_no_such_page_found'));
        }
        $integration->active = abs($integration->active - 1);
        $integration->save();
        return successResponse(trans('Facebook::lang.facebook_status_change'));
    }

    /**
     * Returns the view with create form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('facebook::create-edit');
    }

    /**
     * Returns the view with edit form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($integrationId)
    {
        $facebookIntegration = FacebookCredential::findOrFail($integrationId);
        $integrationData = $facebookIntegration->toArray();
        return view('facebook::create-edit', compact('integrationData'));
    }

    /**
     * Updates the facebook page
     * @param FacebookCredentialsRequest $request
     * @param $integrationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FacebookCredentialsRequest $request, $integrationId)
    {
        $integration = FacebookCredential::find($integrationId);
        if (!$integration) {
            return errorResponse(trans('Facebook::lang.facebook_no_such_page_found'));
        }
        $updated = $integration->update(
            $request->only(['page_id','page_access_token','page_name','verify_token','new_ticket_interval'])
        );
        return ($updated)
            ? successResponse(trans('Facebook::lang.facebook_integration_updated_successful'))
            : errorResponse(trans('Facebook::lang.facebook_integration_update_fail'));
    }

    /**
     * Returns view for Facebook Integration Settings.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsView()
    {
        return view('facebook::settings');
    }

}