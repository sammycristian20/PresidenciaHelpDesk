<?php

namespace App\Plugins\Chat\Controllers\Core;

use Illuminate\Http\Request;
use App\Plugins\Chat\Model\Chat;
use App\Http\Controllers\Controller;
use App\Plugins\Chat\Requests\ChatRequest;
use App\Plugins\Chat\Controllers\Tawk\TawkController;
use App\Plugins\Chat\Controllers\LivServ\ProcessController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends Controller {

    /**
     * The Webhook hits here..
     * @param String $app
     * @param String $dept
     * @param String $helptopic
     * @pa
     */
    public function chat($app, $dept, $helptopic,Request $request) {
        if ($this->checkApp($app)) {
            $controller = $this->chooseApp($app, $request,$dept,$helptopic);
            return $controller->webhookEntry();
        }
    }

    /**
     * Get the dummy response
     * @return array
     */
    public function getRequest($json = '') {
        $json = '{"app":"happy_fox_chat","message":"Chat conversation by vijay (vijaysebastian111@gmail.com) on Aug 23rd 2016\n\n\nvijay : dscghsd - 03:21 PM\nvijay (Agent) : dcbdsh - 03:22 PM\n\n\nStats:\nUrl: http:\/\/localhost\/FaveoVersions\/faveo-helpdesk\/public\/\nWaiting Time: 3 seconds\nChat Duration: 3 seconds\nWebsite Profile: Lady\nOperating System: Mac OS X 10.11\nBrowser: Chrome 52.0\n","email":"vijaysebastian111@gmail.com","name":"vijay"}';
        $array = json_decode($json, true);
        return $array;
    }

    /**
     * check the app
     * @param string $app
     * @return boolean
     */
    private function checkApp($app) {
        
        return (bool) Chat::where('short',$app)->value("status");

    }

    /**
     * choose the application from zapier
     * @param string $app
     * @param array $request
     * @return mixed
     */
    private function chooseApp($app,$request,$dept,$helptopic) {
        switch ($app) {
            case 'liv_serv' :
                return new ProcessController($request,$dept,$helptopic);
            case 'tawk'     :
                return new TawkController($request,$dept,$helptopic);
        }
    }

    /**
     * Return paginated list of all chat services
     * @param \Illuminate\Http\Request $request
     * @param Response
     */
    public function getChats(Request $request)
    {
        $chatServices = Chat::with([
            'helptopic:id,topic as name',
            'department:id,name'
        ]);

        $chatServices->when((bool)($request->ids),function($q) use ($request){
            return $q->whereIn('id',$request->ids);
        });

        $chatServices->when((bool)($request->search_term),function($q) use ($request){
            return $q->where('name','LIKE', "%$request->search_term%");
        });

        
        $chats = $chatServices
                    ->orderBy(
                        (($request->sort_field) ? : 'updated_at'), 
                        (($request->sort_order) ? : 'asc')
                    )
                    ->paginate((($request->limit) ? : '10'))
                    ->toArray();
        $chats['chats'] = $chats['data'];
        unset($chats['data']);
        return successResponse('',$chats);  
    }

    /**
     * Changes the status of chat service 
     * @param mixed $id
     * @return Response
     */
    public function statusChange($id)       
    {
        $chat = Chat::find($id);
        if($chat) {
            //toggle
            $updated_status = abs($chat->status - 1);
           return ( $chat->update(['status' => $updated_status])) 
           ? successResponse(trans('chat::lang.status_changed')) 
           : errorResponse(trans('chat::lang.status_change_error'));
        }
    }

    /** 
     * Displays the chat service edit page
     * @param mixed $id
     * @return View
    */
    public function edit($id)
    {
        $chat = Chat::find($id);
        if($chat) 
            return view("chat::edit",compact('id'));
        throw new NotFoundHttpException();    
    }

    /**
     * Displays the chats settings Page
     * @param void
     * @return view
     */
    public function chatSettingsPage()
    {
        return view("chat::settings");
    }

    /**
     * Persists the chat service info.
     * @param  ChatRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function persistChatService(ChatRequest $request, $id)
    {
        $chat = Chat::findOrFail($id);
        $updated = $chat->update([
             'department' => $request->department['id'],
             'helptopic'  => $request->helptopic['id'],
             'secret_key' => $request->secret_key,
             'url'        => $request->url,
             'script'      => $request->script
        ]);

        return ($updated)
            ? successResponse(trans('chat::lang.updated_successfully'))
            : errorResponse(trans('chat::lang.update_error'));
    }

}
