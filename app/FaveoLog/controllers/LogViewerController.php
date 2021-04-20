<?php
namespace App\FaveoLog\controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use App\FaveoLog\LaravelLogViewer;

class LogViewerController extends Controller
{
    /**
     * get the logs list
     * @return view
     */
    public function index()
    {
        if (Request::input('l')) {
            //dd(base64_decode(Request::input('l')));
            LaravelLogViewer::setFile(base64_decode(Request::input('l')));
            
        }
        if (Request::input('dl')) {
            return Response::download(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('dl'))));
        } elseif (Request::filled('del')) {
            File::delete(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('del'))));
            return Redirect::to(Request::url());
        }


        return View::make('log::log', [
            'l' => Request::input('l'),
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }

    /**
     * get the logs list
     * 
     * @return view
     */
    public function systemLogs()
    {
        return view('log::system-log');
    }

    /**
     * data from db to json
     * @param string $date
     * @return json
     */
    public function logApi($date=""){
        if ($date) {
            //dd(base64_decode(Request::input('l')));
            LaravelLogViewer::setFile(base64_decode($date));
            
        }
        $logs = LaravelLogViewer::all();
        $collection = collect($logs);
        return \DataTables::of($collection)
        ->editColumn('text', function ($model) {
            $str = $model['text'];
            if (strlen($model['text']) > 70) {
                $content = htmlentities($model['text']);
                // dump($content);
                $str = substr($str, 0, 70).'&nbsp<div class="readmore-tooltip">'.trans('lang.read-more').'<span class="readmore-tooltip-text"><iframe srcdoc="'.$content.'" width="100%" style="border:none;color:red"></iframe></span></div>';
            }
            return $str;
        })
        ->rawColumns(['text'])
        ->make();
        
    }
}
