<?php

namespace App\Http\Controllers\Admin\helpdesk;

use Mail;
use File;
use App\User;
use Exception;
use ZipArchive;
use Carbon\Carbon;
use App\Backup_path;
use Illuminate\Http\Request;
use App\Model\Api\ApiSetting;
use RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use App\Model\helpdesk\Settings\Email;
use App\Model\helpdesk\Settings\Backup;
use App\Http\Controllers\Agent\helpdesk\Notifications\NotificationController as Notify;


    /**
     * Class to perform Database and System backup
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @date   2019-11-22T13:56:42+0530
     *
     */
class BackupController extends Controller
{

    public function index()
    {
        return view('themes.default1.admin.helpdesk.settings.backup');
    }

        /**
     * 
     * @param Request $request
     * @return type json
     */
    public function getindex(Request $request)
    {
        try {

            $pagination = ($request->input('limit')) ? $request->input('limit') : 10;

            $sortBy = ($request->input('sort-field')) ? $request->input('sort-field') : 'id';
            $search = $request->input('search-query');
            $orderBy = ($request->input('sort-order')) ? $request->input('sort-order') : 'asc';
            $baseQuery = Backup::select('id', 'filename', 'db_name', 'version', 'created_at')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function($q) use ($search) {
                        $q->where('version', 'LIKE', '%' . $search . '%');
                    })
                    ->paginate($pagination);
            return successResponse($searchQuery);
        } catch (Exception $ex) {
            /* redirect to Index page with Success Message */
            return errorResponse($ex->getMessage());
        }
    }

    public function getPhpExecutableFromPath() 
    {
      $paths = explode(PATH_SEPARATOR, getenv('PATH'));
      foreach ($paths as $path) {
        // we need this for XAMPP (Windows)
        if (strstr($path, 'php.exe') && isset($_SERVER["WINDIR"]) && file_exists($path) && is_file($path)) {
            return $path;
        }
        else {
            $phpExecutable = $path . DIRECTORY_SEPARATOR . "php" . (isset($_SERVER["WINDIR"]) ? ".exe" : "");
            if (file_exists($phpExecutable) && is_file($phpExecutable)) {
               return $phpExecutable;
            }
        }
      }
      return FALSE; // not found
    }

    /**
     * Sanitizes all the special characters that are not allowed for Database passwords
     *
     * @date   2020-02-24T13:54:34+0530
     *
     * @param  string $dbPass  The password of the database
     *
     * @return string          The sanitized password
     */
    public function sanitizePassword($dbPass, $system)
    {
        $symbols = ['^','&','<','|','>','%'];
        if ($system == 'Windows') {
            foreach ($symbols as $symbol) {
           if(strpos($dbPass, $symbol) !== false) {
            $dbPass = str_replace($symbol, '^'.$symbol, $dbPass); // ^ is considered as escape character in Windows
           }
         }
        } else {
            //If there was a special character in database password for ubuntu or Linux servers, the file backup was not getting generated as this was getting interpreted into shell script character. So, we put the db passsword into single quotes to ensure that the password is treated as a single value and then in the above line we escape any single quotes (') in the password so it doesn't gets considered as closing of the single quote.
            $dbPass = str_replace("'", "'\\''", $dbPass);
       }
       return $dbPass;
        
    }

    
    /**
     * First checks for the valid path permissions where the backup is to be stored.If permissions are correct system backup starts
     * and a mail is sent after the backup is complete
     *
     *
     * @date   2019-11-22T13:59:04+0530
     *
     * @param  Request $request
     *
     */
    

    public function takeSystemBackup(Request $request)
    {
        try {
        // $sumOfDbAndFilesystem = intval($this->getDatabaseAndFileSystemSize());//Getting only integer part of the sum 

        // $availableSpace = $this->checkForAvailableSpace($sumOfDbAndFilesystem);
        
        // if(!$availableSpace) {
        //     throw new \Exception(trans('lang.disk_space_error', ["space"=> getSize($sumOfDbAndFilesystem * 1024 *1024)]));
        //     //Converting into bytes first before getting the size through getSize helper
        // }

        $path = $request->input('path');
        $file = new Filesystem();
        if(!$file->isDirectory($path)) {
           $file->makeDirectory($path, 0777, true, true);
            } elseif($file->isDirectory($path)) {
              if(!is_readable($path) || !is_writable($path)) {
                throw new \Exception(trans('lang.give_rwx_permission'));
              }
            }
            if(strpos(realpath($path), base_path() ) !== false) {
            throw new \Exception(trans('lang.give_valid_path'));
          }
          Backup_path::updateOrCreate(['id'=>1],['backup_path'=>$path]);



        $folderPath =  base_path();
        $backupPath = Backup_path::pluck('backup_path')->first();
        $currentVersion = \Config::get('app.tags');
        $dbUser = \Config::get('database.connections.mysql.username');

        $dbPass = \Config::get('database.connections.mysql.password');
        $database = \Config::get('database.connections.mysql.database');

        $currentTimestamp = Carbon::now()->timestamp;
        $finalPath = $backupPath.DIRECTORY_SEPARATOR.date('Y/m/d').DIRECTORY_SEPARATOR."filesystem-$currentTimestamp";
        $fPath = $backupPath.DIRECTORY_SEPARATOR.date('Y/m/d');
        $dbZipPath =$backupPath.DIRECTORY_SEPARATOR.date('Y/m/d').DIRECTORY_SEPARATOR."db-$currentTimestamp";
        $email = \Auth::user()->email;
        $path =  \Config::get('app.url');
        $autoUpdate = $request->input('autoUpdate');
            

       if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {//if OS is Windows
           if(exec("7z") == "") { //If 7z-Zip for windows is not installed
            return errorResponse(trans('lang.install_7-zip', ["user"=> exec('whoami')]));
           }

            if(function_exists('popen') && function_exists('pclose')) {//If popen/pclose PHP functions are enabled
            $phpExePath = $this->getPhpExecutableFromPath();//get the PHP executable path for windows
            $artisanPath = $phpExePath." ".$folderPath.DIRECTORY_SEPARATOR.'artisan backup:email --email='.$email." ".'--ver='.$currentVersion." ".'--autoUpdate='.$autoUpdate;
            $databaseUpdate = $phpExePath." ".$folderPath.DIRECTORY_SEPARATOR.'artisan database:sync';
            $sanitizePassword = $this->sanitizePassword($dbPass, $system = 'Windows');


            if($autoUpdate) { //If backup is being taken up from Auto Update module then first take backup and then update the files
                 \Artisan::call('down');
                 $dbPass == '' ? pclose(popen("start cmd /k \"7z a -tzip {$finalPath} {$folderPath} && mysqldump -u{$dbUser} {$database}  | 7z a -si {$dbZipPath}.7z /B && $artisanPath && $databaseUpdate"." \"","r")) : pclose(popen("start cmd /k \"7z a -tzip {$finalPath} {$folderPath} && mysqldump -u$dbUser -p\"$sanitizePassword\" $database | 7z a -si {$dbZipPath}.7z /B && $artisanPath && $databaseUpdate"." \"","r")) ;
             } else {
                 $dbPass == '' ? pclose(popen("start cmd /k \"7z a -tzip {$finalPath} {$folderPath} && mysqldump -u{$dbUser} {$database}  | 7z a -si {$dbZipPath}.7z /B && $artisanPath"." \"","r")) : pclose(popen("start cmd /k \"7z a -tzip {$finalPath} {$folderPath} && mysqldump -u$dbUser -p\"$sanitizePassword\" $database | 7z a -si {$dbZipPath}.7z /B && $artisanPath"." \"","r")) ;
             }
           


                Backup::create(['filename'=>"Filesystem_{$currentVersion}",'db_name'=>"Database_$currentVersion",'file_path'=>$finalPath.'.zip' ,'db_path'=>$dbZipPath.'.7z','version'=> $currentVersion]);

                    $successResponse = ($autoUpdate) ? successResponse(trans('lang.backup_update_started')) : successResponse(trans('lang.backup_started'));
                     return $successResponse;
                } else {
                    return errorResponse(trans('lang.enable-popen/pclose'));
                 }
                } elseif(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && function_exists('exec')) {//If OS is not windows
                    if(exec("zip") == "") { //If zip is not installed 
                    return errorResponse(trans('lang.install-zip'));
                   }
                    $this->makeDirectory($fPath);
                    $sanitizePassword = $this->sanitizePassword($dbPass, $system = 'Not_Windows');
                    $phpExePath = PHP_BINDIR;//get php executable path other than windows
                    $artisanPath = $phpExePath.'/php'." ".$folderPath.DIRECTORY_SEPARATOR.'artisan backup:email --email='.$email." ".'--ver='.$currentVersion." ".'--autoUpdate='.$autoUpdate;
                    $databaseUpdate = $phpExePath.'/php'." ".$folderPath.DIRECTORY_SEPARATOR.'artisan database:sync';
                     if($autoUpdate) { //If backup is being taken up from Auto Update module then first take backup and then update the files
                    \Artisan::call('down');
                   exec("(mysqldump -u{$dbUser} -p'{$sanitizePassword}' {$database} | zip {$dbZipPath} - ; zip -r {$finalPath} {$folderPath} ; $artisanPath ; $databaseUpdate) > /dev/null 2>/dev/null &");
                    } else {
                        exec("(mysqldump -u{$dbUser} -p'{$sanitizePassword}' {$database} | zip {$dbZipPath} - ; zip -r {$finalPath} {$folderPath} ; $artisanPath) > /dev/null 2>/dev/null &");
                    }

                    Backup::create(['filename'=>"Filesystem_{$currentVersion}",'db_name'=>"Database_$currentVersion",'file_path'=>$finalPath.'.zip' ,'db_path'=>$dbZipPath.'.zip','version'=> $currentVersion]);

                    $successResponse = ($autoUpdate) ? successResponse(trans('lang.backup_update_started')) : successResponse(trans('lang.backup_started'));
                     return $successResponse;
                    } else {
                        return errorResponse(trans('lang.enable-exec'));
                    }

                } catch (Exception $ex) {
                return errorResponse($ex->getMessage());
            }

    }

   /**
    * Checks whether there is enough disk space to perform system backup
    *
    * @date   2020-03-13T15:39:38+0530
    *
    * @param  float      Sum of Size of db and filesystem
    *
    * @return boolean
    */
    protected function checkForAvailableSpace(int $sumOfDbAndFilesystem) : bool
    {
        $diskFreeSpace =  disk_free_space(base_path()) /1024 /1024; //In MB
        $allowBackup = intval($diskFreeSpace) > $sumOfDbAndFilesystem;
        return $allowBackup;
    }

    /**
     * Get the sum of size of Database + Filesystem
     *
     * @date   2020-03-13T15:34:40+0530
     *
     * @return float $sum   
     */
    public function getDatabaseAndFileSystemSize() : float
    {
        $filesystemSize = 250; // Assuming it to be 250 mb.

        //Now we shall calculate the database size
        $host = \Config::get('database.connections.mysql.host');
        $username = \Config::get('database.connections.mysql.username');
        $password = \Config::get('database.connections.mysql.password');
        $database = \Config::get('database.connections.mysql.database');
        
        $name = @mysqli_connect($host,$username,$password); 

        $sql = "SELECT table_schema 'db_name', SUM( data_length + index_length) / 1024 / 1024 'db_size_in_mb' FROM information_schema.TABLES WHERE table_schema='$database' GROUP BY table_schema ;";
         $query = mysqli_query($name, $sql);
         $data = mysqli_fetch_array($query); 
         $sum = $filesystemSize + (2 * $data['db_size_in_mb']);
         return $sum;
    }


    /**
     * Get the backup path from the  database
     *
     * @date   2019-11-13T15:58:29+0530
     *
     * @return json
     */
    public function getBackupPath()
    {
        $path =Backup_path::pluck('backup_path')->first();
        return successResponse('', ['path' => $path]); 
     }


    /**
     * method to create directory based on given path
     * @param $path
     *@return $path
     */
    private function makeDirectory($path)
    {
        $file = new Filesystem();
        if (!$file->isDirectory($path)) {
            $file->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Download the DB/Filesystem after the backup is complete
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @date   2019-11-22T14:02:19+0530
     *
     * @param  int $id The Id of the backup file
     * @param  string $type  Filesystem or database to be downloaded
     *
     */
    public function downloadBackup($id,$type)
    {
        try {
            $path = 'db_path';
            $name = 'db_name';
            if($type == 'filesystem') {
                $path = 'file_path';
                $name = 'filename';
            }
            $path = Backup::where('id',$id)->value($path);
            $name = Backup::where('id', $id)->value($name);
            if (!ob_get_level()) ob_start();// activating output buffer if not enabled on server
            header('Content-type: Zip');
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename='.$name.'.zip');
            header('Content-Length:'.filesize($path));
            ob_end_clean();
            flush();
            readfile($path);
        } catch( Exception $ex) {
           return errorResponse($ex->getMessage());
        }
    }

    /**
     * Delete the Filesystem and Db backup
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @param  int $id  The id of the backup to be deleted
     * @return json
     */
    public function deleteBackup($id) 
    {
      try{
        $backup = Backup::where('id', $id)->first();
        if($backup) {
             unlink($backup->file_path);
             unlink($backup->db_path);
            $backup->delete();
            return successResponse(trans('lang.backup_deleted_successful'));
        } else {
        return errorResponse(trans('lang.no_record_found'));
      } 
    } catch (Exception $ex) {
        return errorResponse($ex->getMessage());
      }
      
    } 
    
}
