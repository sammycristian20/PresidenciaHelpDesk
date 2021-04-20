<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Model\Api\ApiSetting;
use App\Http\Controllers\Common\Request\CreateUpdateWebhookRequest;

/**
 * Webhook controller for handling webhook creation and updation
 */
class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware('role.admin');
    }

    /**
     * method for webhook create and edit blade page
     * @return view
     */
    public function webhookCreateAndEditPage()
    {
        return view('themes.default1.common.api.webhook');
    }

    /**
     * method to create and update webhook
     * @param CreateUpdateWebhookRequest $request
     * @return JSON Response
     */
    public function createUpdateWebhook(CreateUpdateWebhookRequest $request) {
        if (!is_null($request->web_hook)) {
            ApiSetting::updateOrCreate(['key' => 'web_hook'],[
                'key' => 'web_hook',
                'value' => $request->web_hook
             ]);
        }
        
        return successResponse(trans('lang.webhook_saved_successfully'));

    }

    /**
     * method to get webhook data
     * @return $webhook
     */
    public function getWebhookData() {
        $webhook = ApiSetting::where('key', 'web_hook')->value('value');

        return successResponse('', ['web_hook' => $webhook]);
    }
}
