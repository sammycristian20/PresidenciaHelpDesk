<?php

namespace App\FaveoStorage\database\seeds;

use App\Facades\Attach;
use Illuminate\Http\UploadedFile;
use App\FileManager\Models\FileManagerAclRule;
use App\Model\helpdesk\Ticket\Ticket_attachments;

class DatabaseSeeder extends \database\seeds\DatabaseSeeder
{
    public function run()
    {
        $this->copyTicketAttachmentsToNewDisk();
    }

     /**
     * Update ticket attachments with new path
     */
    private function copyTicketAttachmentsToNewDisk()
    {
        $attachments = Ticket_attachments::where('driver', 'local')->cursor();

        foreach ($attachments as $attachment) {
            $fileName = $attachment->path . '/' . $attachment->name;

            preg_match('/(\d{4})\/(\d{2})\/(\d{2})/', str_replace('\\', "/", $attachment->path), $matches);

            $folderStructure = ($matches) ? '/' . $matches[1] . '/' . $matches[2] . '/' . $matches[3] . '/' : '';

            $newPath = (\File::exists($fileName))
               ? Attach::put('ticket_attachments' . $folderStructure, $this->getUploadedFileObject($fileName), 'system', null, false)
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

     /**
     * Returns the UploadedFile Object from the `path` passed.
     * @param $path
     * @return UploadedFile
     */
    private function getUploadedFileObject($path): UploadedFile
    {
        return new UploadedFile($path, basename($path), null, 0, false);
    }
}
