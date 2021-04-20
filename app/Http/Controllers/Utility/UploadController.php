<?php

namespace App\Http\Controllers\Utility;

use App\FaveoStorage\Controllers\StorageController;
use App\Http\Requests\helpdesk\Common\ChunkUploadRequest;
use Illuminate\Http\Request as LaravelRequest;
use App\Http\Controllers\Controller;
//use Flow\Request;
use Response;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('role.agent');
    }

    public function upload(ChunkUploadRequest $request)
    {
        try {

          
            //modifying request param to flow compatibility.
            //it recieves parameters which can be made compatible with \Flow by simply adding flow prefix 
            $this->modifyRequestParamsToFlow($_REQUEST);
            
            $request     = new \Flow\Request();
            $destination = $this->fileName($this->getPrivateDir(), $request);
            //$destination = $this->getPrivateDir() . '/' . $request->getFileName();
            $config      = $this->setConfig();
            $file        = new \Flow\File($config, $request);
            $response    = Response::make('', 200);

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$file->checkChunk()) {
                    return Response::make('', 404);
                }
            }
            else {
                if ($file->validateChunk()) {
                    $file->saveChunk();
                }
                else {
                    // error, invalid chunk upload request, retry
                    return Response::make('', 400);
                }
            }
            if ($file->validateFile() && $file->save($destination)) {
                $mime      = \File::mimeType($destination);
                $extension = \File::extension($destination);
                if (mime($mime) != 'image' || mime($extension) != 'image') {
                    //chmod($destination, 1204);
                }
                $response = Response::make('success', 200);
            }
            return $response;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            return response()->json(compact('result'), 500);
        }
    }
     
    /**
     * It adds prefix 'flow' to each request parameter so that it can be compatible with 'flow/flow-php-server' dependency
     * @param array $request    $_Request array with the paramters which has to be appended with prefix flow
     * @return bool             true if success else false
     */
    private function modifyRequestParamsToFlow(&$request){
        if(is_array($request)){
        foreach($request as $key => $value){

                $newKey = (strpos($key, 'flow') === false) ? 'flow'.ucfirst($key) : $key;
                $request[$newKey] = $value;
                
                if($newKey != $key){
                    unset($request[$key]);                    
                }
            }
            return true;
        }           
        return false;
    }

    public function filename($dir, $request)
    {
        $destination = $this->getPrivateDir() . '/' . $request->getFileName();
        if (\File::exists($destination)) {
            $destination = $this->getPrivateDir() . '/' . str_random(4) . '_' . $request->getFileName();
        }
        return $destination;
    }
    public function filenamePublic($dir, $request)
    {
        $destination = $this->getPublicDir() . '/' . $request->getFileName();
        if (\File::exists($destination)) {
            $destination = $this->getPublicDir() . '/' . str_random(4) . '_' . $request->getFileName();
        }
        return $destination;
    }
    public function setConfig()
    {
        $config      = new \Flow\Config();
        $temp_folder = storage_path('chunk'); //$this->getPrivateDir() . '/chunk';
        \File::makeDirectory($temp_folder, 0775, true, true);
        $config->setTempDir($temp_folder);
        return $config;
    }
    public function setConfigPublic()
    {
        $config      = new \Flow\Config();
        $temp_folder = storage_path('chunk');
        \File::makeDirectory($temp_folder, 0775, true, true);
        $config->setTempDir($temp_folder);
        return $config;
    }
    public function getPrivateDir()
    {
        $settings = new \App\Model\helpdesk\Settings\CommonSettings();
        $private  = $settings->getOptionValue('storage', 'private-root');
        $year     = date('Y');
        $month    = date('n');
        $day      = date('j');
        $dir      = storage_path('app/private/' . $year . '/' . $month . '/' . $day);
        if ($private) {
            $dir = $private->option_value . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day;
        }
        if (!\File::isDirectory($dir)) {
            \File::makeDirectory($dir, 0775, true);
        }
        if (!\File::isWritable($dir)) {
            throw new \Exception("$dir need write permission");
        }
        return $dir;
    }
    public function getPublicDir()
    {
        $year  = date('Y');
        $month = date('n');
        $day   = date('j');
        $dir   = public_path('uploads/' . $year . '/' . $month . '/' . $day);
        if (!\File::isDirectory($dir)) {
            \File::makeDirectory($dir, 0775, true);
        }
        if (!\File::isWritable($dir)) {
            throw new \Exception("$dir need write permission");
        }
        return $dir;
    }
    public function getPrivate()
    {
        $settings = new \App\Model\helpdesk\Settings\CommonSettings();
        $private  = $settings->getOptionValue('storage', 'private-root');
        if ($private) {
            $dir = $private->option_value;
        }
        else {
            $dir = storage_path('app/private');
        }
        //dd($dir);
        return $dir;
    }
    public function files(\Illuminate\Http\Request $request)
    {
        $page      = $request->input('page', 1);
        $perPage   = 5;
        $offset    = ($page * $perPage) - $perPage;
        $directory = $this->dir($request->all());
        //$files = \File::allFiles($directory);
        $files     = collect(\File::allFiles($directory))
                ->sortByDesc(function ($file) {
                    return $file->getMTime();
                });
        $requestedFileType = checkArray('type', $request->all());
        if (!empty($requestedFileType)) {
            $files = $files->filter(function($value, $key) use ($requestedFileType) {
                $fileType = \File::mimeType($value->getPathname());
                if ($requestedFileType == 'doc') {
                    if (strpos($fileType, 'text') !== false || strpos($fileType, 'application') !== false) {
                        return $value;
                    }
                } elseif(strpos($fileType, strtolower($requestedFileType)) !== false) {
                    return $value;   
                }
                
                return false;
            });
        }
        $files = $files->toArray();
        $file_contents = [];
        $file_five     = array_slice($files, $offset, $perPage);

        foreach ($file_five as $key => $file) {
            chmod($file->getPathname(), 0775);
            $mime                             = \File::mimeType($file->getPathname());
            $file_contents[$key]['pathname']  = $file->getPathname();
            $file_contents[$key]['extension'] = $file->getExtension();
            $file_contents[$key]['filename']  = $file->getFilename();
            $file_contents[$key]['size']      = $file->getSize();
            $file_contents[$key]['type']      = substr($mime, 0, strpos($mime, "/"));
            $file_contents[$key]['path']      = $file->getPath();
            $file_contents[$key]['modified']  = date("F j, Y, g:i a", \File::lastModified($file->getPathname()));
            if (starts_with($mime, 'image')) {
                list($width, $height) = getimagesize($file->getPathname());
                $file_contents[$key]['width']   = $width;
                $file_contents[$key]['height']  = $height;
                $file_contents[$key]['type']    = 'image';
            }
            $file_contents[$key]['thumbnail_url'] = (new StorageController)->getThumbnailUrlByPath($file->getPathname());

        }
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($file_contents, count($files), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return $paginator->toJson();
    }
    public function filesSearch(\Illuminate\Http\Request $request)
    {
        $term  = $request->input('term');
        $dir   = $this->getPrivate() . DIRECTORY_SEPARATOR . $term . ".*";
        $files = glob($dir);
        if (count($files) > 0)
                foreach ($files as $file) {
                $info = pathinfo($file);
                echo "File found: extension " . $info["extension"] . "<br>";
            }
        else echo "No file name exists called $term. Regardless of extension.";
    }
    public function dir($request = [])
    {
        $directory = $this->getPrivate();
        if (checkArray('year', $request)) {
            $directory = $directory . DIRECTORY_SEPARATOR . checkArray('year', $request);
        }
        if (checkArray('year', $request) && checkArray('month', $request)) {
            $directory = $directory . DIRECTORY_SEPARATOR . checkArray('month', $request);
        }
        if (checkArray('year', $request) && checkArray('month', $request) && checkArray('day', $request)) {
            $directory = $directory . DIRECTORY_SEPARATOR . checkArray('day', $request);
        }
        if (!is_dir($directory)) {
            abort(401, 'Invalid directory');
        }
        return $directory;
    }
    public function dirPublic($request = [])
    {
        $directory = public_path('uploads');
        if (checkArray('year', $request)) {
            $directory = $directory . DIRECTORY_SEPARATOR . checkArray('year', $request);
        }
        if (checkArray('year', $request) && checkArray('month', $request)) {
            $directory = $directory . DIRECTORY_SEPARATOR . checkArray('month', $request);
        }
        if (checkArray('year', $request) && checkArray('month', $request) && checkArray('day', $request)) {
            $directory = $directory . DIRECTORY_SEPARATOR . checkArray('day', $request);
        }
        if (!is_dir($directory)) {
            throw new \Exception('Invalid directory', 401);
        }
        return $directory;
    }
    public function uploadPublic(ChunkUploadRequest $request)
    {
        try {
            //modifying request param to flow compatibility.
            //it recieves parameters which can be made compatible with \Flow by simply adding flow prefix 
            $this->modifyRequestParamsToFlow($_REQUEST);
            $request = new \Flow\Request();

            $destination = $this->filenamePublic($this->getPublicDir(), $request);
            $config      = $this->setConfigPublic();
            $file        = new \Flow\File($config, $request);
            $response    = Response::make('', 200);

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$file->checkChunk()) {
                    return Response::make('', 404);
                }
            }
            else {
                if ($file->validateChunk()) {
                    $file->saveChunk();
                }
                else {
                    // error, invalid chunk upload request, retry
                    return Response::make('', 400);
                }
            }
            if ($file->validateFile() && $file->save($destination)) {
                $mime      = \File::mimeType($destination);
                $extension = \File::extension($destination);
                if (mime($mime) != 'image' || mime($extension) != 'image') {
                    //chmod($destination, 1204);
                }

                $response = Response::make('success', 200);
            }
            return $response;
        } catch (\Exception $e) {
            $result = $e->getMessage();
            return response()->json(compact('result'), 500);
        }
    }
    public function filesPublic(\Illuminate\Http\Request $request)
    {
        $page      = $request->input('page', 1);
        $perPage   = 5;
        $offset    = ($page * $perPage) - $perPage;
        $directory = $this->dirPublic($request->all());
        //$files = \File::allFiles($directory);
                $files     = collect(\File::allFiles($directory))
                ->sortByDesc(function ($file) {
                    return $file->getMTime();
                });
        $requestedFileType = checkArray('type', $request->all());
        if (!empty($requestedFileType)) {
            $files = $files->filter(function($value, $key) use ($requestedFileType) {
                $fileType = \File::mimeType($value->getPathname());
                if ($requestedFileType == 'doc') {
                    if (strpos($fileType, 'text') !== false || strpos($fileType, 'application') !== false) {
                        return $value;
                    }
                } elseif(strpos($fileType, strtolower($requestedFileType)) !== false) {
                    return $value;   
                }
                
                return false;
            });
        }
        $files = $files->toArray();
        $file_contents = [];
        $file_five     = array_slice($files, $offset, $perPage);

        foreach ($file_five as $key => $file) {
            chmod($file->getPath(), 0775);
            $mime                             = \File::mimeType($file->getPathname());
            $file_contents[$key]['pathname']  = $file->getPathname();
            $file_contents[$key]['extension'] = $file->getExtension();
            $file_contents[$key]['filename']  = $file->getFilename();
            $file_contents[$key]['size']      = $file->getSize();
            $file_contents[$key]['type']      = substr($mime, 0, strpos($mime, "/"));
            $file_contents[$key]['path']      = $file->getPath();
            $file_contents[$key]['base_64']   = $this->getFilePathAsUrl($file);
            $file_contents[$key]['modified']  = date("F j, Y, g:i a", \File::lastModified($file->getPathname()));
            try{
                list($width, $height) = getimagesize($file->getPathname());
            } catch(\Exception $e) {
                list($width, $height) = false;
            }
            $file_contents[$key]['width']     = $width;
            $file_contents[$key]['height']    = $height;
            if (mime($mime) != 'image' || mime($file->getExtension()) != 'image') {
                //chmod($file_contents[$key]['pathname'],1204);
            }
        }
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($file_contents, count($files), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

        return $paginator->toJson();
    }
    public function filesSearchPublic(\Illuminate\Http\Request $request)
    {
        $term  = $request->input('term');
        $dir   = $this->getPrivate() . DIRECTORY_SEPARATOR . $term . ".*";
        $files = glob($dir);
        if (count($files) > 0)
                foreach ($files as $file) {
                $info = pathinfo($file);
                echo "File found: extension " . $info["extension"] . "<br>";
            }
        else echo "No file name exists called $term. Regardless of extension.";
    }
    public function deletefile(LaravelRequest $request)
    {
        $file = $request->input('file');
        if (is_file($file)) {
            unlink($file);
            $message = "$file deleted successfully";
            $status  = 200;
        }
        else {
            $message = "$file not found";
            $status  = 500;
        }
        return response()->json([$message], $status);
    }

    public function listDirectories(\Illuminate\Http\Request $request)
    {   
        try {
            $metaData = ($request->has('meta')) ? $request->input('meta') : false;
            $path     = (!$request->has('directory')) ? $this->getPrivate() : storage_path().DIRECTORY_SEPARATOR.$request->input('directory');
            $list     = $this->getDirectoryList($path);
            $result   = array_map(function($value) use($metaData, $path){
                $name = str_replace($path.DIRECTORY_SEPARATOR, '', $value);
                $data =['name' => $name, 'value' => $name];
                return ($metaData) ? array_merge($data, ['dir_path' => $value]) : $data;
            }, $list);
            return empty($result)? errorResponse(trans('lang.not_found')): successResponse('', $result);
        } catch (\Exception $e) {
            return errorResponse($e->getMessage());
        }
    }

    /**
     * Returns an array containing the list of subdirectories present in given directory path/name
     * 
     * @param  String  $dir
     */
    private function getDirectoryList(String $dir)
    {
        return glob($dir.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR);
    }

    /**
     * @param $file
     * @return string
     */
    private function getFilePathAsUrl($file) : string
    {
        $filePathAsUrl = asset(strstr($file->getPathname(), 'uploads'));
        // ISSUE: image urls are supposed to be URLs instead of file path. So, in windows URLs looks some thing like
        // http://support.faveohelpdek.com/uploads\2019\11\25\test-pattern-152459__340.jpeg. Now if these are URL encoded,
        // `\` will not be treated as `/` . So for workaround, replacing backslash character with slash
        return str_replace('\\', '/', $filePathAsUrl);
    }
}
