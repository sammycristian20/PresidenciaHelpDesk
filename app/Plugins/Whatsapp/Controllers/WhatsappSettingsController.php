<?php

namespace App\Plugins\Whatsapp\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Whatsapp\Model\WhatsApp;
use App\Plugins\Whatsapp\Requests\WhatsAppSettingsRequest;
use Illuminate\Http\Request;

class WhatsappSettingsController extends Controller
{   
    /**
     * Adds WA Business Account Details to the system
     * @pararm App\Plugins\Whatsapp\Requests\WhatsAppSettingsRequest $request
     * @return Response
     */
    public function store(WhatsAppSettingsRequest $request)
    {
        return WhatsApp::create($request->all())
        ? successResponse(trans('Whatsapp::lang.app_created'))
        : errorResponse(trans('Whatsapp::lang.app_create_failed'));
    }

    /**
     * Update existing WA Business Account Details 
     * @pararm App\Plugins\Whatsapp\Requests\WhatsAppSettingsRequest $request
     * @return Response
     */
    public function update(WhatsAppSettingsRequest $request)
    {   
        $wa = WhatsApp::first();
        $updated = $wa->update($request->all());

        return ($updated)
        ? successResponse(trans('Whatsapp::lang.app_updated'))
        : errorResponse(trans('Whatsapp::lang.app_update_failed'));
    }

    /**
     * Destroys the whatsApp App
     * @param String $id
     * @return Response
     */
    public function destroy()
    {   
        $wa = WhatsApp::first();
        return ($wa->delete())
        ? successResponse(trans('Whatsapp::lang.app_deleted'))
        : errorResponse(trans('Whatsapp::lang.app_delete_failed'));
    }

    /**
     * Returns all whatsapp business accounts
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function returnAll(Request $request)
    {
        $query = WhatsApp::query();
        $query->when((bool)($request->search_term),function($q) use ($request){
            return $q->where('name','LIKE', "%$request->search_term%")
            ->orWhere('business_phone','LIKE',"%$request->search_term%");
        });

        $query->when((bool)($request->app_ids),function($q) use ($request){
            return $q->whereIN('id', $request->app_ids);
        });

        $query->when((bool)($request->app_id),function($q) use ($request){
            return $q->where('id', $request->app_id);
        });

        $accounts = $query
                    ->orderBy((($request->sort_field) ? : 'updated_at'), (($request->sort_order) ? : 'asc'))
                    ->paginate((($request->limit) ? : '10'))
                    ->toArray();

        $accounts['accounts'] = $accounts['data'];
        unset($accounts['data']);        
        return successResponse('', $accounts);             
    }

    /**
     * Returns View for Settings Page of Whatsapp Plugin.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsView()
    {
        return view('Whatsapp::whatsapp');
    }

    /**
     * Returns View for Edit Page of Whatsapp Plugin.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editView()
    {
        return view('Whatsapp::edit');
    }
}