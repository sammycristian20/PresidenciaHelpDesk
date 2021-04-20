<?php

namespace App\FaveoStorage\Controllers;

use App\FaveoStorage\Controllers\StorageController;
use App\Model\Common\Attachment;
use App\Model\Common\LinkedAttachment;
use App\Model\helpdesk\Settings\FileSystemSettings;

/**
 * Class for handling attachment storage for different entities like canned response,
 * threads, articles etc. Currently this class extends the existing storage controller
 * which is specifically written for saving attachments of tickets but it must be deprecated
 * and this class should be updated to implement that functionality and save ticket attachments
 * in attachment table itself instead of ticket_attachments to remove redundancy in database.
 * @category Controller
 * @package  App\FaveoStorage
 * @author   Manish Verma <manish.verma@ladybirdweb.com>
 * @since    v1.9.47
 * @todo     Implement storage class's functionalities in this class to store attachments of tickets in `attachments`
 *           table and linked_attachments table. Migrate data from ticket_attachments table and update dependencies of
 *           tickets to make attachments working with this new class.
 */
class AttachmentStoreController extends StorageController
{

    /**
     *
     *
     */
    public function storeAttachments(array $file, string $disposition)
    {
        return self::saveAttachmentsDetailsInDB($file, $disposition);
    }

    /**
     *
     *
     */
    private function saveAttachmentsDetailsInDB(array $file, string $disposition)
    {
        $attachment = Attachment::updateOrCreate([
            'name'        => $file['filename'],
            'file_size'   => $file['size'],
            'file_type'        => $file['type'],
            'disposition' => $disposition,
            'path'        => $file['path']
        ], [
            'name'        => $file['filename'],
            'file_size'   => $file['size'],
            'file_type'        => $file['type'],
            'disposition' => $disposition,
            'path'        => $file['path'],
            'driver'      => FileSystemSettings::value('disk') //Changin it as during cleaning of StorageController the prop had been removed
        ]);
        return $attachment->id;
    }
}
