<?php

namespace App\Plugins\Chat\Controllers\Core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plugins\Chat\Model\Chat;

class SettingsController extends Controller {

    // public function settings() {
    //     $chat = new Chat();
    //     $apps = include base_path('app/Plugins/Chat/Chat.php');
    //     $departments = \App\Model\helpdesk\Agent\Department::pluck('name','id')->toArray();
    //     $topics = \App\Model\helpdesk\Manage\Help_topic::pluck('topic','id')->toArray();
    //     dd($chat);
    //     return view("chat::core.settings", compact('apps', 'chat','departments','topics'));
    // }

    public function chatSettingsPage()
    {
        return view("chat::settings");
    }

    public function activateIntegration($app, Request $request) {
        $chat = new Chat();
        $status = $request->input('status');
        $this->deleteChat($app);
        $chat->create([
            'name' => studly_case($app),
            'short' => $app,
            'status' => $status,
        ]);
    }

    public function deleteChat($app) {
        $chats = new Chat();
        $chat = $chats->where('short', $app)->first();
        if ($chat) {
            $chat->delete();
        }
    }

    public function sendTest() {
        $values = $this->dummyValues();
        echo "<form action='http://localhost/faveo/Faveo-Helpdesk-Pro-fork/public/chat/data/liv_serv/2/helptopic' method='post' name='redirect'>";
        echo $values;
        echo '</form>';
        echo "<script language='javascript'>document.redirect.submit();</script>";
    }

    public function dummyValues() {
        $values = "";
        $json = '{
        "app":"liv_serv",
        "message":"http://chat.livservmart.net:8080/bnaindia/workTemplats/chatMsg4client.jsp?chVisitid=xxxxxxxx&comcode=xxxx&yea=xxxx",
        "email":"vijaysebastian111@gmail.com",
        "name":"vijay",
        "phno":"9988776655",
        "helptopic":1,
        "title":"visitor want to know the price details of 3bhk"
        }
        ';
        $array = json_decode($json);
        foreach ($array as $key => $value) {
            $values .="<input type='text' name='" . $key . "' value='" . $value . "'>";
        }
        return $values;
    }
    
    public function ajax(Request $request){
        $model = $request->input('model');
        $modelid = $request->input('modelid');
        $app = $request->input('app');
        $url = url("chat/$app/$modelid/$model");
        $name = $this->modelIdName($model, $modelid);
        return "<div><h3>".$name." ".ucfirst($model)."</h3></div><div><pre>$url</pre></div>";
    }
    
    public function modelIdName($model,$modelid){
        $select = 'name';
        if($model=='helptopic'){
            $model = 'help_topic';
            $select = 'topic';
        }
        $schema = \DB::table($model)->where('id',$modelid)->select($select)->first();
        if($schema){
            return ucfirst($schema->$select);
        }
    }

}
