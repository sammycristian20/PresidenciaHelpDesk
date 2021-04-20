<?php

namespace App\Plugins\Envato\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Plugins\Envato\Model\Envato;
use App\Plugins\Envato\Model\EnvatoPurchase;
use App\Plugins\Envato\Controllers\TicketFormController;

class EnvatoSettingsController extends Controller
{
    public function SettingsForm()
    {
        $settings   = new Envato;
        $purchase   = new EnvatoPurchase;
        $settings   = $settings->where('id', '1')->first();
        if ($settings) {
            return view('envato::settings', compact('settings'));
        }
        else {
            $settings = '';
            return view('envato::settings', compact('settings'));
        }
    }
    public function PostSettings(Request $request)
    {
        $this->validate($request, ['token' => 'required','client_id'=>'required'],['token.required'=>'Secret key required']);
        $envatos = new Envato;
        $envato  = $envatos->where('id', '1')->first();
        if ($envato) {
            $envato->fill($request->input())->save();
            return redirect()->back()->with('success', 'Saved Successfully');
        }
        else {
            $envatos->fill($request->input())->save();
            return redirect()->back()->with('success', 'Saved Successfully');
        }
    }
    public function TopNav()
    {
        echo "<li id=bar><a href=" . url('envato/settings') . " >Envato</a></li></a></li>";
    }
    public function statusChange($event)
    {
        $name   = checkArray('name', $event);
        $status = checkArray('status', $event);
        if ($name == 'Envato' && $status == 0) {
            \Artisan::call('remove:field', ['form' => 'ticket', '--unique' => 'purchase_code']);
        }
    }
    
}
