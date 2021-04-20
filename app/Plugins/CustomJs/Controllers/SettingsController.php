<?php

namespace App\Plugins\CustomJs\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\CustomJs\Model\CustomJs;
use Illuminate\Database\Schema\Blueprint;
use Schema;
use Lang;
use Exception;
use Auth;
use App\Plugins\SyncPluginToLatestVersion;
use App\Model\helpdesk\Settings\Plugin;

class SettingsController extends Controller {

    public function __construct()
    {
        // syncing ldap to latest version
        (new SyncPluginToLatestVersion)->sync('CustomJs');
    }

    public function activate()
    {
        if(!Schema::hasTable('customjs')) {
            return $this->migrate();
        }
    }

    public function migrate()
    {
        try{
            $path = "app".DIRECTORY_SEPARATOR."Plugins".DIRECTORY_SEPARATOR."CustomJs".DIRECTORY_SEPARATOR."database".DIRECTORY_SEPARATOR."migrations";
            return \Artisan::call('migrate', [
            '--path' => $path,
            '--force'=>true,
            ]);
        } catch (Exception $ex) {
            // dd($ex);
        }
    }

    public function settings()
    {
        try {
            //return custom js table view with table format
            return view('CustomJs::index');
        } catch(\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function getJsTable()
    {
        $js_data = CustomJs::select('name', 'parameter', 'fired_at', 'fire', 'id')->get();
        return \DataTables::of($js_data)
        ->editColumn('name', function($model){
            return utfEncoding($model->name);
        })
        ->editColumn('fire', function($model){
            if ($model->fire) {
                return '<span class="btn btn-xs btn-default" style="pointer-events:none;color:green">'.Lang::get('lang.active').'</span>';
            }
            return '<span class="btn btn-xs btn-default" style="pointer-events:none;color:red">'.Lang::get('lang.inactive').'</span>';
        })
        ->addColumn('action', function($model){
            return '<a href="' . route('customjs.edit', $model->id) . '" class="btn btn-primary btn-xs "><i class="fa fa-edit" style="color:white;"> &nbsp;</i> ' . Lang::get('lang.edit') . '</a>&nbsp;&nbsp;'
                                    . '<a href="' . route('customjs.delete', $model->id) . '" class="btn btn-primary btn-xs" onClick="return confirmDelete('.$model->id.')"><i class="fa fa-trash" style="color:white;"> &nbsp;</i> ' . Lang::get('lang.delete') . '</a>&nbsp;&nbsp;';
        })
        ->rawColumns(['fire', 'action'])
        ->make();
    }

    public function create()
    {
        try {
            $customjs = null;
            return view("CustomJs::create", compact('customjs'));
        } catch(\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function store(Request $request, CustomJs $customjs)
    {
        $request->validate([
            'name'      => 'required|unique:customjs|max:50',
            'parameter' => 'max:50',
            'fired_at'  => 'required'
        ]);
        try {
            $customjs->fill($request->all());
            if($customjs->save()) {
                return redirect()->back()->with('success', Lang::get('CustomJs::lang.customjs-saved-successfuly'));
            }
            return redirect()->back()->with('fails', Lang::get('CustomJs::lang.customjs-can-not-be-saved-try-again'))->withInput();
        } catch(\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $customjs = CustomJs::whereId($id)->first();
        if($customjs) {
            return view("CustomJs::edit", compact('customjs'));
        }
        return redirect()->back()->with('fails', Lang::get('CustomJs::lang.custom-js-not-found'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'      => 'required|max:50|unique:customjs,name,' . $request->segment(3),
            'parameter' => 'max:50',
            'fired_at'  => 'required'
        ]);
        try {
            $customjs = CustomJs::whereId($request->segment(3))->first();
            if($customjs) {
                $customjs->fill($request->all());
                if($customjs->save()) {
                    return redirect()->back()->with('success', Lang::get('CustomJs::lang.customjs-updated-successfuly'));
                }
            }
            return redirect()->back()->with('fails', Lang::get('CustomJs::lang.customjs-can-not-be-saved-try-again'))->withInput();
        } catch(\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $table = CustomJs::where('id', $id)->delete();
            if ($table) {
                return redirect()->back()->with('success', Lang::get('CustomJs::lang.custom-js-deleted-successfully'));
            }
            return redirect()->back()->with('fails', Lang::get('CustomJs::lang.custom-js-not-found'));
        } catch(\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Function to get all get routes grouped by admin and agent middleware
     * 
     * @return Response 
     */
    public function getRoutes()
    {
        $routeCollection = \Route::getRoutes();
        $routes = ['agent' =>[], 'admin' =>[]];
        foreach ($routeCollection as $value) {
            try{
                if($value->methods()[0] === "GET") {
                    if(in_array('role.agent', $value->gatherMiddleware()) || $value->uri == "thread/{ticketId}") {
                        $this->pushRoute($routes, 'agent', $value->uri);
                    } elseif(in_array('roles', $value->gatherMiddleware())) {
                        $this->pushRoute($routes, 'admin', $value->uri);
                    }
                }
            } catch(\Exception $e){
                continue;
            }
        }

        return successResponse('', $routes);
    }

    /**
     * Function to push uri to given sub array of given array with given key
     * @param  Array   $routes  array containing routes
     * @param  Srting  $key     key of subarray to which $uri should be added
     * @param  String  $url     uri to be added
     * @return void
     */
    private function pushRoute(array &$routes, string $key, string $uri):void
    {
        if(stripos($uri, 'api') === false) {
            array_push($routes[$key], $uri);
        }
    }
}
