<?php

namespace App\FileManager\Helpers;

use App\FileManager\Models\FileManagerAclRule;
use App\Model\helpdesk\Settings\System;
use App\User;
use Storage;

class StoreFileMetadataHelper
{
    private $disk;
    private $copyType;
    private $oldDisk;

    public function __construct($disk, $copyType = 'copy', $oldDisk = '')
    {
        $this->disk = $disk;
        $this->copyType = $copyType;
        $this->oldDisk = $oldDisk;
    }

    public function scanForFilesInDirectory($directory = '')
    {
        $nodes = Storage::disk($this->disk)->listContents($directory);

        foreach ($nodes as $node) {
            if ($node['type'] === 'dir') {
                $this->addToFileManagerAclModel($this->disk, $node);
                $this->scanForFilesInDirectory($node['path']);
            } else {
                $this->addToFileManagerAclModel($this->disk, $node);
            }
        }
    }

    private function addToFileManagerAclModel($disk, $node)
    {
        if ($this->copyType === 'copy') {
            $aclModel =  FileManagerAclRule::updateOrCreate([
                "disk" => $disk,
                "path" => $node['path'],
            ], [
                "disk" => $disk,
                "path" => $node['path'],
                "access" => 2, //write permission for the user.
                "user_id" =>  User::where('role', 'admin')->value('id'),
                "type" => ($node['type'] === 'dir') ? 'directory' : 'file',
                "basename" => $node['basename'],
                "dirname" => $node['dirname']
            ]);

            $aclModel->departments()->create(['department_id' => System::value('department')]);
        } elseif ($this->copyType === 'cut' && $this->oldDisk) {
            FileManagerAclRule::where(['path' => $node['path'],'disk' => $this->oldDisk,'type' => $node['type']])
                ->update(['disk' => $this->disk]);
        }
    }
}
