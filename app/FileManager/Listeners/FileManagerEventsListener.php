<?php

namespace App\FileManager\Listeners;

use App\FileManager\Helpers\PasteHelper;
use App\FileManager\Helpers\StoreFileMetadataHelper;
use App\FileManager\Jobs\PasteFilesJob;
use App\FileManager\Jobs\StoreFileMetaDataJob;
use App\FileManager\Models\FileManagerAclRule;
use App\FileManager\Models\FileManagerAclRuleDepartment;
use App\Http\Controllers\Common\PhpMailController;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FileManagerEventsListener
{
    private function handleFileUploadEvent($event)
    {
        $fileInfoObjects = $event->files();

        foreach ($fileInfoObjects as $fileInfoObject) {
            $path = (Str::startsWith($fileInfoObject['path'], '/')) ? substr($fileInfoObject['path'], 1) : $fileInfoObject['path'];

            $this->addToFileManagerAclModel($event->disk(), $path, 'file', $event->type());
        }
    }

    private function addToFileManagerAclModel($disk, $path, $type, $page)
    {
        $userAndDepartment = $this->getUserAndDepartments();

        $dirName = dirname($path);

        $hidden = (in_array($page, ['kb','page'])) ? 0 : 1;

        $aclModel =  FileManagerAclRule::updateOrCreate(
            [
                "disk" => $disk,
                "path" => $path,
            ],
            [
                "disk" => $disk,
                "path" => $path,
                "access" => 2,
                "user_id" => $userAndDepartment['user_id'],
                "type" => $type,
                "basename" => basename($path),
                "dirname" => ($dirName === '.') ? '' : $dirName,
                "hidden" => $hidden
            ]
        );

        ($hidden) ? $aclModel->departments()->saveMany($userAndDepartment['departments']) : null;
    }

    private function handleFileAndDirectoryCreatedEvent($event, $type)
    {
        $pathForStoring = ($event->path()) ? $event->path() . "/" . $event->name() : $event->name();

        $this->addToFileManagerAclModel($event->disk(), $pathForStoring, $type, $event->page());
    }

    /**
     * Returns BaseQuery for FileManagerAclRule
     * @return Builder
     */
    private function getAclBaseQuery(): Builder
    {
        return FileManagerAclRule::query();
    }

    /**
     * Base query for files
     * @return Builder
     */
    private function getFilesBaseQuery(): Builder
    {
        $aclBaseQuery = $this->getAclBaseQuery();
        return $aclBaseQuery->where('type', 'file');
    }

    /**
     * Deletes all files in directory
     * @param $directory
     */
    private function deleteFilesOfDirectory($directory)
    {
        $fileQuery = $this->getFilesBaseQuery();

        $fileQuery->where('dirname', $directory);

        $this->deleteAndUnlinkDepartments($fileQuery);
    }

    /**
     * Base query for folders
     * @return Builder
     */
    private function getDirectoriesBaseQuery(): Builder
    {
        $aclBaseQuery = $this->getAclBaseQuery();
        return $aclBaseQuery->where('type', 'directory');
    }

    /**
     * Deletes the directory parameter
     * @param $directory
     */
    private function deleteDirectory($directory)
    {
        $this->deleteFilesOfDirectory($directory);

        $directoryQuery = $this->getDirectoriesBaseQuery();

        $directoryQuery->where('path', $directory);

        $this->deleteAndUnlinkDepartments($directoryQuery);
    }

    /**
     * Delete entries along with their respective departments
     * @param Builder $query
     */
    private function deleteAndUnlinkDepartments(Builder $query)
    {
        $query->get()->each(function ($item) {
            $item->departments()->delete();
            $item->delete();
        });
    }

    /**
     * Deletes file parameter
     * @param $file
     */
    private function deleteFile($file)
    {
        $fileQuery = $this->getFilesBaseQuery();

        $fileQuery->where('path', $file);

        $this->deleteAndUnlinkDepartments($fileQuery);
    }

    /**
     * Handles File Manager deleted event
     * @param $event
     */
    private function handleDeletedEvent($event)
    {
        $deletedItems = $event->items();

        foreach ($deletedItems as $deletedItem) {
            ($deletedItem['type'] === 'dir')
                ? $this->deleteDirectory($deletedItem['path'])
                : $this->deleteFile($deletedItem['path']);
        }
    }

    /**
     * Gets array of department ids the user belongs to.
     * @param User $user
     * @return array
     */
    private function getDepartmentsOfUser(User $user): array
    {
        return array_column($user->departments()->get()->toArray(), 'id');
    }

    /**
     * Returns array consisting of user Id and Department
     * @return array
     */
    private function getUserAndDepartments(): array
    {
        $user = \Auth::user();

        $userDepartments = $this->getDepartmentsOfUser($user);

        $departmentsForInserting = [];

        foreach ($userDepartments as $department) {
            $departmentsForInserting[] = new FileManagerAclRuleDepartment(['department_id' => $department]);
        }

        return ['user_id' => $user->id, "departments" => $departmentsForInserting];
    }

    /**
     * Handles FileManager `paste` event
     * @param $event
     */
    private function handlePasteEvent($event)
    {
        $pasteInfo = $event->clipboard();

        $pasteOperation = $pasteInfo['type'];

        $newDirectory = ($event->path()) ? $event->path() . "/" : '';

        foreach ($pasteInfo['directories'] as $directory) {
            if ($pasteOperation === 'cut') {
                $this->deleteDirectory($directory);
            }

            $baseDirectoryName = basename($directory);

            $this->addToFileManagerAclModel($event->disk(), "$newDirectory$baseDirectoryName", "directory", $event->page());
        }

        foreach ($pasteInfo['files'] as $file) {
            if ($pasteOperation === 'cut') {
                $this->deleteFile($file);
            }

            $baseFileName = basename($file);

            $this->addToFileManagerAclModel($event->disk(), "$newDirectory$baseFileName", "file", $event->page());
        }
    }

    private function handleRenameEvent($event)
    {
        $baseQuery = $this->getAclBaseQuery();
        $baseQuery->where(['path' => $event->oldName(), 'type' => $event->type()])
            ->first()
            ->update(['path' => $event->newName()]);
    }

    public function handleFileManagerEvents($event, $type)
    {
        switch ($type) {
            case 'file-upload':
                $this->handleFileUploadEvent($event);
                break;
            case 'directory-created':
                $this->handleFileAndDirectoryCreatedEvent($event, "directory");
                break;
            case 'file-created':
                $this->handleFileAndDirectoryCreatedEvent($event, "file");
                break;
            case 'deleted':
                $this->handleDeletedEvent($event);
                break;
            case 'paste':
                $this->handlePasteEvent($event);
                break;
            case 'rename':
                $this->handleRenameEvent($event);
                break;
        }
    }

    public function pasteFilesAndDirectoriesOnDiskChange($disks)
    {
        $fileSysSettings = FileSystemSettings::first(['paste_on_disk_change','paste_type']);

        if ($fileSysSettings && $fileSysSettings->paste_on_disk_change) {
            (new PhpMailController())->setQueue();

            PasteFilesJob::withChain([
                new StoreFileMetaDataJob(
                    new StoreFileMetadataHelper($disks['new'], $fileSysSettings->paste_type, $disks['old'])
                )
            ])
            ->dispatch(new PasteHelper($fileSysSettings->paste_type, $disks['old'], $disks['new']));
        }
    }
}
