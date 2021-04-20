<?php
namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App;
// requests
use App\Http\Controllers\Controller;
//supports
use App\Model\helpdesk\Settings\System;
use Config;
//classes
use File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Input;
use Lang;
use Validator;
use UnAuth;
use Auth;
use Cache;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\Plugin;

/**
 * Handles all the language related operations like language change, getting languages etc.
 */
class LanguageController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return type void
     */
    public function __construct()
    {
        $this->middleware('auth',['except'=>'getLanguageFile']);
        $this->middleware('roles',['except'=>'getLanguageFile']);
    }

    /**
     * Switch language at runtime.
     *
     * @param type "" $lang
     *
     * @return type response
     */
    public function switchLanguage($lang)
    {
        $changed = UnAuth::changeLanguage($lang);
        if (!$changed) {
            return \Redirect::back()->with('fails', Lang::get('lang.language-error'));
        } else {
            return \Redirect::back();
        }
    }

    /**
     * Shows language page.
     *
     * @return type response
     */
    public function index()
    {
        return view('themes.default1.admin.helpdesk.language.index');
    }

    /**
     * Provide language datatable to language page.
     *
     * @return type
     */
    public function getLanguages()
    {
        $path = base_path('resources/lang');
        $values = scandir($path);  //Extracts names of directories present in lang directory
        $values = array_slice($values, 2); // skips array element $value[0] = '.' & $value[1] = '..'
        return \DataTables::collection(new Collection($values))
                ->addColumn('language', function ($model) {

                    $img_src = assetLink('image','flag').'/'. $model . '.png';
                    if ($model == Config::get('app.fallback_locale')) {
                        return '<img src="' . asset($img_src) . '"/>&nbsp;' . Config::get('languages.' . $model)[0] . ' (' . Lang::get('lang.fallback_locale') . ')';
                    } else {
                        return '<img src="' . asset($img_src) . '"/>&nbsp;' . Config::get('languages.' . $model)[0];
                    }
                })
                ->addColumn('native-name', function ($model) {
                    return Config::get('languages.' . $model)[1];
                })
                ->addColumn('id', function ($model) {
                    return $model;
                })
                ->addColumn('status', function ($model) {
                    $system = System::select('content')->where('id', 1)->first();
                    $sys_lang = $system->content;
                    if ($sys_lang === $model) {
                        return "<span class='btn btn-xs btn-default' style='color:green;pointer-events:none;'>" . Lang::get('lang.yes') . '</span>';
                    } else {
                        return "<span class='btn btn-xs btn-default' style='color:red;pointer-events:none;'>" . Lang::get('lang.no') . '</span>';
                    }
                })
                ->addColumn('action', function ($model) {
                    $system = System::select('content')->where('id', 1)->first();
                    $sys_lang = $system->content;
                    if ($sys_lang === $model) {
                        return "<a href='change-language/" . $model . "'><input type='button' class='btn btn-primary btn-xs ' disabled value='" . Lang::get('lang.make-default') . "'/></a>";
                    } else {
                        return "<a href='change-language/" . $model . "'><input type='button' class='btn btn-primary btn-xs ' value='" . Lang::get('lang.make-default') . "'/></a>";
                    }
                })
                ->rawColumns(['action','language','status'])
                ->make();
    }

    /**
     * allow user to download language template file.
     *
     * @return type
     */
    public function download()
    {
        $path = 'downloads' . DIRECTORY_SEPARATOR . 'en.zip';
        $file_path = public_path($path);

        return response()->download($file_path);
    }

    /**
     * Gets language file content as array based on current language chosen
     * by the user (if not chosen by the user then language chosen by the admin will be fetched)
     * NOTE : currently we are caching the entire language file, but this has to change
     * @param \Illuminate\Http\Request $request
     * @return array                                language file as a single array
     */
    public function getLanguageFile()
    {
        $languages = array_unique([Lang::getFallback(), App::getLocale()]);
        
        $languageArray =[];
        
        foreach ($languages as $lang) {
           $this->appendCoreLanguage($lang, $languageArray);
           $this->appendPluginLanguage($lang, $languageArray);
           $this->appendModuleLanguage($lang, $languageArray);
        }
        
        header('Content-Type: text/javascript');
        // caching for 30 days
        header("Cache-Control: max-age=2592000");
        echo('translator = ' . json_encode($languageArray) . ';');
        exit();
    }

    /**
     * Fetches language array of given language for core Helpdesk and merges
     * it in $languageArray
     * 
     * @param   string  $languageName
     * @param   array   $languageArray
     * @param   array   $languageArray
     * @return  void
     */
    private function appendCoreLanguage(string $languageName, Array &$languageArray) :void
    {
        $path = resource_path('lang/' . $languageName);
        $this->updateLanguageArray($path, $languageArray);
    }

    /**
     * Fetches language array of given language for additional modules and merges
     * it in $languageArray
     *
     * @var     array   $moduleLangPaths  array containing path to lang directory in
     *                                    different modules
     * @param   string  $languageName
     * @param   array   $languageArray
     * @return  void
     */
    private function appendModuleLanguage(string $languageName, Array &$languageArray)
    {
        $moduleLangPaths = ['TimeTrack/resources/lang', 'Bill/lang', 'FaveoReport/lang', "FaveoLog/lang", "FileManager/lang"];
        foreach ($moduleLangPaths as $value) {
            $path = app_path($value.DIRECTORY_SEPARATOR. $languageName);
            $this->updateLanguageArray($path, $languageArray);
        }
    }

    /**
     * Fetches language array of given language for active plugins and merges
     * it in $languageArray
     *
     * @param   string  $languageName
     * @param   array   $languageArray
     * @return  void
     */
    private function appendPluginLanguage(string $languageName, Array &$languageArray) :void
    {
        $activatePlugins = Plugin::where('status',1)->pluck('name');
        foreach ($activatePlugins as $plugin) {
            $path = app_path("Plugins/$plugin/lang/$languageName");
            $this->updateLanguageArray($path, $languageArray);
        }
    }

    /**
     * Returns an array of filenames with .php extension in given directory path
     *
     * @param   string  $path  path to directory from which .php files
     * @return  array          empty array if given path is not a directory otherwise
     *                         array containing app .php filenames with path
     */
    private function getLanguageFileArray(string $path) :array
    {
        if(!is_dir($path)) return [];

        return glob($path.DIRECTORY_SEPARATOR."*.php");
    }

    /**
     * Function which actually fetches language array data from all ".php" lanaguge
     * files availanle in the given path and merges that into $languageArray
     *
     * @param   string  $path
     * @param   array   $languageArray
     * @return  void
     */
    private function updateLanguageArray(string  $path, &$languageArray) :void
    {
        $files = $this->getLanguageFileArray($path);
        foreach ($files as $file) {
            $name           = basename($file, '.php');
            // merge lang files with same name
            if (array_key_exists($name, $languageArray)) {
                $languageArray[$name] = array_merge($languageArray[$name], require $file);
            } else {
                $languageArray[$name] = require $file;
            }
        }
    }
}
