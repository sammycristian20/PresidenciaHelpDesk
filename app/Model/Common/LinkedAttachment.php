<?php 

namespace App\Model\Common;

use App\BaseModel;

/**
 * Model for handling linking of attachments with their respective category in database such as ticket
 * articles etc
 * @category Model
 * @package  App\Model\Common
 * @author   Manish Verma <manish.verma@ladybirdeb.com>
 * @since    v1.9.47
 * @todo     Remove ticket_attachemnts table and implement new storage method using this model
 */
class LinkedAttachment extends BaseModel
{
    protected $table = "linked_attachments";
    
    protected $fillable = [
        /**
         * Foreign key referenced to id of 
         * attachments table
         */
        'attachment_id',

        /**
         * id of the entity to which the attachment is linked
         * for example if of ticket_thread, canned responses etc
         */
        'category_id',

        /**
         * type of the entity to which the attachment is linked 
         * for example thread, canned, article, comments etc.
         */
        'category_type'
    ];

    /**
     * Get attachment files
     * @return Attachments
     */
    public function attachments()
    {
        return $this->belongsTo('App\Model\Common\Attachments', 'attachment_id');
    }

    /**
     * Get all related category models
     */
    public function category()
    {
        return $this->morphTo();
    }
}
