<?php

namespace App\Model\Common;

use App\BaseModel;

/**
 * Model for handling attachments in database
 * @category Model
 * @package  App\Model\Common
 * @author   Manish Verma <manish.verma@ladybirdeb.com>
 * @since    v1.9.47
 * @todo     - Remove ticket_attachemnts table and implement new storage method using this model
 *           - Figure out how to create a column to store files in blob/medium blob to store files in database.
 */
class Attachment extends BaseModel
{
    protected $table = "attachments";
    
    protected $fillable = [
        'id',
        /**
         * stores string name of the file with extension
         */
        'name',

        /**
         * stores the size of file in bytes
         */
        'file_size',

        /**
         * stores MIME type of files
         */
        'file_type',

        /**
         * stores disposition of files for emails.
         * eg. INLINE, ATTACHMENTS
         */
        'disposition',

        /**
         * stores storage driver used to store file
         * eg local (for file system), database
         */
        'driver',

        /**
         * Relative path of file which are saved in file system
         */
        'path'
    ];

    public function getNameAttribute($value)
    {
        return basename($value);
    }
}
