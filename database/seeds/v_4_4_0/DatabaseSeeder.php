<?php

namespace database\seeds\v_4_4_0;

use GuzzleHttp\Client;
use App\FileManager\Models\FileManagerAclRule;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Settings\System;
use App\User;
use App\Model\helpdesk\Agent\UserPermission;
use App\Model\helpdesk\Settings\Ticket;
use Schema;
use DB;
use Exception;
use Logger;
use database\seeds\DatabaseSeeder as Seeder;
use App\Model\helpdesk\Utility\Timezones;
use App\Model\helpdesk\Utility\Date_format as DateFormat;
use App\Model\helpdesk\Utility\Time_format as TimeFormat;
use App\Facades\Attach;
use App\FileManager\Helpers\PasteHelper;
use App\Model\Common\Attachment;
use App\Model\helpdesk\Agent_panel\Organization;
use App\Model\helpdesk\Settings\Company;
use App\Model\helpdesk\Theme\Portal;
use File;
use Illuminate\Http\UploadedFile;
use App\Model\Common\TicketActivityLog;
use App\Model\kb\Article;

class DatabaseSeeder extends Seeder
{
     public function __construct()
    {
        require_once(public_path('script/apl_core_configuration.php'));
        require_once(public_path('script/apl_core_functions.php'));
    }


    public function run()
    {
        $this->permissionsSeeder();
        $this->updateUserPermissionsSeeder();
        $this->seedAgentsCount();
        $this->updateTimeZoneIdInSystemSettingSeeder();
        $this->dateFormatSeeder();
        $this->timeFormatSeeder();
        $this->redemptionOfTicketSettings();
        $this->createDirectoriesToStoreUserProfileImageAndOrganizationLogo()
            ->runArtisanStorageLink()
            ->moveAllUserProfileImagesToNewDirectory()
            ->moveAllOrganizationLogosToNewDirectory()
            ->moveCompanyLogosAndIconsToNewDisk()
            ->copyCannedAttachmentsToNewDisk();

        if (! FileSystemSettings::value('files_moved_from_old_private_disk')) {
            $this->copyOldFileManagerMediaToNewPrivateDisk()
                ->renameDiskNameAndSetVisibility();
        }
        $this->fixCorruptedTicketActivityLog();
        $this->enableCommentOnExistingKBArticles();
    }

    /**
     * method for seeder to add permissions to permissions table
     * @return null
     */
    private function permissionsSeeder()
    {   
        $permissionsList = $this->listOfPermission();

        foreach ($permissionsList as $permission) {
            UserPermission::updateOrCreate($permission, $permission);
        }
    }

    /**
     * method for seeder to update user permissions to user_permission pivot table
     * @return null
     */
    private function updateUserPermissionsSeeder()
    {  
        try {
            if (Schema::hasTable('permision')) {
                $permissionsWithLinkedUserId = DB::table('permision')->get(['id','user_id','permision']);
                if (!$permissionsWithLinkedUserId->isEmpty()) {
                    foreach ($permissionsWithLinkedUserId as $permissionsData) {
                        $userId = $permissionsData->user_id;
                        $user = User::find($userId);
                        if ($user) {
                            // admin does not need any specific permission, they have access throughout the system
                            if ($user->role == 'admin') {
                                continue;
                            }
                            $permissions = json_decode($permissionsData->permision);
                            $permissionIds = [];
                            foreach ($permissions as $key => $value) {
                                $permissionId = UserPermission::where('key', $key)->value('id');
                                if ($key && $value && $permissionId) {
                                   array_push($permissionIds, $permissionId);
                                }
                            }
                            $user->permissions()->sync($permissionIds);
                        }
                    }
                }
            }
             
        } catch (Exception $exceptionData) {
            Logger::exception($exceptionData);
        } 
    }

    /**
     * method for permission data
     * @return array $permissions
     */
    private function listOfPermission()
    {
        $permissions = [
            ['key' => 'create_ticket', 'name' => 'Create ticket'],
            ['key' => 'edit_ticket', 'name' => 'Edit ticket'],
            ['key' => 'close_ticket', 'name' => 'Close tickets'],
            ['key' => 'transfer_ticket', 'name' => 'Transfer Ticket'],
            ['key' => 'delete_ticket', 'name' => 'Delete tickets'],
            ['key' => 'assign_ticket', 'name' => 'Tickets Assigned'],
            ['key' => 'view_unapproved_tickets', 'name' => 'View unapproved tickets'],
            ['key' => 'apply_approval_workflow', 'name' => 'Apply Approval Workflow'],
            ['key' => 'access_kb', 'name' => 'Access knowledge base'],
            ['key' => 'report', 'name' => 'Access reports'],
            ['key' => 'organisation_document_upload', 'name' => 'Upload organization documents'],
            ['key' => 'change_duedate', 'name' => 'Change duedate'],
            ['key' => 're_assigning_tickets', 'name' => 'Re assigning tickets'],
            ['key' => 'global_access', 'name' => 'Global access'],
            ['key' => 'restricted_access', 'name' => 'Restricted access (view only tickets assigned to them)'],
            ['key' => 'access_user_profile', 'name' => 'Access user profile'],
            ['key' => 'access_organization_profile', 'name' => 'Access organization profile'],
            ['key' => 'recur_ticket', 'name' => 'Recur Ticket'],
            ['key' => 'user_activation_deactivaton', 'name' => 'Activate or Deactivate user account'],
            ['key' => 'agent_activation_deactivaton', 'name' => 'Activate or Deactivate agent account'],
            ['key' => 'delete_user_account', 'name' => 'Delete user account'],
            ['key' => 'delete_agent_account', 'name' => 'Delete user account'],
        ];

        return $permissions;
    }

    /**
     * The method is for older clients whose last 4 digits of lic code is non numeric(Since we need last 4 characters to be numeric to put restriction on the no of agents). If that be  the case(last 4 digits is non numeric), Faveo will connect to billing to update the license code of the client based on their product and install faveo with the newly generated license code on the fly.
     */
    public function seedAgentsCount()
    {
        if(!\Schema::hasTable("faveo_license")){
            return;
        }

         $licenseCode = \DB::table('faveo_license')->pluck('LICENSE_CODE')->first();
         $agents = substr($licenseCode, -4);
         if(!is_numeric($agents) && file_exists(base_path('.env')) && \Config::get('database.install') == 1) {//If it is not fresh installation
           $client = new Client([]);
           $productPlan = \Config::get('app.version');
            $source = 'https://billing.faveohelpdesk.com/update/lic-code?licenseCode='.$licenseCode.'&product='.$productPlan;
            $response = $client->request(
                'POST',
                $source
            ); 
            $result =json_decode($response->getBody()->getContents());
        if ($result->status == "success") {
            if(\Schema::hasTable('faveo_license')) {
            \Schema::drop('faveo_license');
        }
            $licenseCode = $result->updatedLicenseCode;
            $url = url('/');
            $host = \Config::get('database.connections.mysql.host');
            $username = \Config::get('database.connections.mysql.username');
            $password = \Config::get('database.connections.mysql.password');
            $database = \Config::get('database.connections.mysql.database');
            $GLOBALS["mysqli"]= @mysqli_connect($host, $username, $password, $database); //establish connection to MySQL database
            $license_notifications = aplInstallLicense($url,"",$licenseCode,$GLOBALS["mysqli"]); //install personal (code-based) license using MySQL database
            } 
        }
          
    } 

    /**
     * method to update timeZoneId 
     * earlier it was storing timeZoneName
     * @return null
     */
    private function updateTimeZoneIdInSystemSettingSeeder()
    {
       $timeZoneName = System::first()->time_zone_id;
       if (!is_numeric($timeZoneName)) {
           $timeZoneId = Timezones::where('name', $timeZoneName)->value('id');
           System::first()->update(['time_zone_id' => $timeZoneId]);
       }
    }

    /**
     * method to seed extra date formats in date_format table
     * @return null
     */
    private function dateFormatSeeder()
    {
       $dateFormats = [
        // 'php date format' => 'JavaScript date format'
        'd-m-Y' => 'DD-MM-YYYY', 
        'm-d-Y' => 'MM-DD-YYYY', 
        'Y-m-d' => 'YYYY-MM-DD', 
        'F j, Y' => 'LL'
       ];

       foreach ($dateFormats as $phpDateFormat => $javaScriptDateFormat) {
           DateFormat::updateOrCreate([
            'format' => $phpDateFormat,
            'js_format' => $javaScriptDateFormat,
            'is_active' => 1
        ]);
       }
    }

    /**
     * method to seed extra time format in time_format table
     * @return null
     */
    private function timeFormatSeeder()
    {
        TimeFormat::updateOrCreate([
            'format' => 'g:i a', 
            'is_active' => 1, 
            'hours' => '12 Hours',
            'js_format' => 'hh:mm a'
        ]);

        TimeFormat::where('format', 'H:i:s')->update([
            'is_active' => 1, 
            'hours' => '24 Hours',
            'js_format' => 'HH:mm'
        ]);
    }

    /**
     * Method checkes custom_field_name column of settings_ticket table for incorrect string
     * values and removes it.
     * Considering the column only saves Integer ids of custom fields.
     * @return void
     */
    private function redemptionOfTicketSettings()
    {
        foreach (Ticket::all() as $ticketSetting) {
            $customFieldsArray = array_filter($ticketSetting->custom_field_name, function($item) {
                return is_numeric($item);
            });
            $ticketSetting->update([
                'custom_field_name' => implode(',', $customFieldsArray)
            ]);
        };
    }

    /**
     * Creates directories in `storage/app/public` to hold all the attachments which were/are (going) to
     * present in `public/uploads/*`
     * @return DatabaseSeeder
     */
    private function createDirectoriesToStoreUserProfileImageAndOrganizationLogo(): DatabaseSeeder
    {
        if (! File::exists(storage_path() . '/app/public')) {
            File::makeDirectory(storage_path() . '/app/public');
        }
        FileSystemSettings::first()->update(['disk' => 'system']);
        $storageAdapter = \Storage::disk('system');
        $storageAdapter->makeDirectory('profile'); //for user profile images
        $storageAdapter->makeDirectory('organization_logo'); //for organization logo
        $storageAdapter->makeDirectory('service_desk'); //for SD attachments
        $storageAdapter->makeDirectory('tinymce_attachments'); //for tinymce copied attachments
        $storageAdapter->makeDirectory('multimedia_private'); //private media files
        $storageAdapter->makeDirectory('multimedia_public'); //public media files
        $storageAdapter->makeDirectory('company_logos'); //logos and icons
        $storageAdapter->makeDirectory('ticket_attachments'); //ticket attachments
        $storageAdapter->makeDirectory('package_attachments'); //ticket attachments
        $storageAdapter->makeDirectory('canned_attachments'); //ticket attachments
        return $this;
    }

    /**
     * Generates a symbolic link to public/storage from storage/app/public
     * @return DatabaseSeeder
     */
    private function runArtisanStorageLink(): DatabaseSeeder
    {
        if (! File::exists('public/storage')) {
            \Artisan::call('storage:link');
        }
        return $this;
    }

    /**
     * moves files from public/uploads/profilepic/* to storage/app/public/profile/{uuid-folder-name}/*
     * @return DatabaseSeeder
     */
    private function moveAllUserProfileImagesToNewDirectory(): DatabaseSeeder
    {
        //used `DB` facade instead of `User` facade due to use of `accessors` for `profile_pic` in `User Model`
        $users = User::cursor();

        foreach ($users as $user) {
            if (! $user->getOriginal('profile_pic')) {
                continue;
            }

            $profilePicturePath = public_path() . "/uploads/profilepic/" . $user->getOriginal('profile_pic');


            $newProfilePicturePath = File::exists($profilePicturePath)
                ? Attach::put('profile', $this->getUploadedFileObject($profilePicturePath))
                : '';

            if ($newProfilePicturePath) {
                $user->profile_pic = Attach::getUrlForPath($newProfilePicturePath, null, 'public');
                try {
                    $user->save();
                } catch (Exception $exception) {
                    Logger::exception($exception);
                }
            }
        }

        return $this;
    }

    /**
     * moves files from public/uploads/company/* to storage/app/public/organization_logo/{uuid-folder-name}/*
     * @return DatabaseSeeder
     */
    private function moveAllOrganizationLogosToNewDirectory(): DatabaseSeeder
    {
        $organizations = Organization::cursor();

        foreach ($organizations as $organization) {
            if (! $organization->logo) {
                continue;
            }

            $organizationLogoPath = public_path() . $organization->logo;

            $newOrganizationLogoPath = File::exists($organizationLogoPath)
                ? Attach::put('organization_logo', $this->getUploadedFileObject($organizationLogoPath))
                : '';

            if ($newOrganizationLogoPath) {
                $organization->logo = Attach::getUrlForPath($newOrganizationLogoPath, null, 'public');
                $organization->save();
            }
        }

        return $this;
    }

    /**
     * Returns the UploadedFile Object from the `path` passed.
     * @param $path
     * @return UploadedFile
     */
    private function getUploadedFileObject($path): UploadedFile
    {
        return new UploadedFile($path, basename($path), null, 0, false);
    }


    /**
     * Copies files and folders in storage/app/private/* to storage/app/public/media_attachments/* for
     * attachment segregation
     * @return DatabaseSeeder
     */
    private function copyOldFileManagerMediaToNewPrivateDisk(): DatabaseSeeder
    {
        (new PasteHelper('copy', storage_path() . '/app/private', config('filesystems.disks.system.root') . '/multimedia_private'))
            ->pasteFilesAndFolders();

        (new PasteHelper('copy', public_path() . '/uploads', config('filesystems.disks.system.root') . '/multimedia_public'))
            ->pasteFilesAndFolders();

        return $this;
    }

    /**
     * Renames 'private` and `public` disks to system and updates visibility
     */
    private function renameDiskNameAndSetVisibility()
    {
        FileManagerAclRule::where('disk', 'private')->update(['hidden' => 1,'disk' => 'system', 'path' => \DB::raw('CONCAT("multimedia_private/",path)')]);

        FileManagerAclRule::where('disk', 'public')->update(['hidden' => 0,'disk' => 'system', 'path' => \DB::raw('CONCAT("multimedia_public/",path)')]);

        FileSystemSettings::first()->update(['files_moved_from_old_private_disk' => 1]);
    }

    /**
     * Movies Company logos to new location
     * @return DatabaseSeeder
     */
    private function moveCompanyLogosAndIconsToNewDisk(): DatabaseSeeder
    {
        $company = Company::first();

        if (! $company->logo_driver && $company->logo) {
            $logoPath = public_path() . '/uploads/company/' . $company->getOriginal('logo');

            $newLogoPath = File::exists($logoPath)
                ? Attach::put('company_logos/logos', $this->getUploadedFileObject($logoPath))
                : '';

            if ($newLogoPath) {
                $company->logo = Attach::getUrlForPath($newLogoPath, null, 'public');
                $company->save();
            }
        }

        $portal = Portal::first();

        if (! $portal->logo_icon_driver) {
            if ($portal->icon) {
                $portalLogoPath = public_path() . '/uploads/icon/' . $portal->getOriginal('icon');

                $newPortalLogoPath = File::exists($portalLogoPath)
                    ? Attach::put('company_logos/icons', $this->getUploadedFileObject($portalLogoPath))
                    : '';

                if ($newPortalLogoPath) {
                    $portal->icon = Attach::getUrlForPath($newPortalLogoPath, null, 'public');
                }
            }

            if ($portal->logo) {
                $portalLogoPath = public_path() . '/uploads/logo/' . $portal->getOriginal('logo');

                $newPortalLogoPath = File::exists($portalLogoPath)
                    ? Attach::put('company_logos/logos', $this->getUploadedFileObject($portalLogoPath))
                    : '';

                if ($newPortalLogoPath) {
                    $portal->logo = Attach::getUrlForPath($newPortalLogoPath, null, 'public');
                }
            }
        }

        $portal->save();

        return $this;
    }

    /**
     * Update canned attachments with new path
     */
    private function copyCannedAttachmentsToNewDisk()
    {
        $attachments = Attachment::where('driver', 'local')->cursor();

        foreach ($attachments as $attachment) {
            $fileName = $attachment->path . '/' . $attachment->name;

            $newPath = (\File::exists($fileName))
                ? Attach::put('canned_attachments', $this->getUploadedFileObject($fileName), 'system', null, false)
                : '';

            if ($newPath) {
                $fullPath = Attach::getFullPath($newPath); //path with filename ex: /storage/app/attachments/hello.png

                $path =  strstr($fullPath, $newPath, true) ?: $fullPath;

                $attachment->name = $newPath;

                $attachment->path = $path;

                $attachment->driver = 'system';

                $attachment->save();
            }
        }
    }

    /*
     * Function deletes activity log contiaing empty array or object string
     *
     * Additional:
     * In some systems having attachment fields in ticket form if the ticket was created with  
     * values in attachment fields then the ticket activity logs do not load because in specific
     * case the attachment data gets inserted which otherwise is set to be ignored from inserting
     * into activity logs. In such case the "value" column of ticket_activity_logs table contains
     * values as below
     * - []
     * - {}
     * - [{},...],
     * - {[],...}
     * 
     * NOTE: To fix this issue and allow infected system to load activity logs this method checks
     * the database table for such values and removes them. It allows us to avoid connecting to each
     * and every client facing this issue and simply ask them to run the udpate for the fix.
     * @return void
     */
    private function fixCorruptedTicketActivityLog()
    {
        TicketActivityLog::where('value', 'LIKE', '%[]%')->orWhere('value', 'LIKE','%{}%')->delete();
    }

    /**
     * This method will enable comments on all existing KB articles 
     * which is disabled by default.
     */
    private function enableCommentOnExistingKBArticles()
    {
        Article::where('is_comment_enabled', 0)->update(['is_comment_enabled' => 1]);
    }
}
