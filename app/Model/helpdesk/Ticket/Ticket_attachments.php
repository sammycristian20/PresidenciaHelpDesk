<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;
use App\FaveoStorage\Controllers\StorageController;
use Config;
use Crypt;

class Ticket_attachments extends BaseModel
{

    protected $table    = 'ticket_attachment';
    protected $fillable = [
        'id', 'thread_id', 'name', 'size', 'type', 'file', 'data', 'poster', 'updated_at', 'created_at', 'path', 'driver', 'content_id'
    ];

    protected $appends = ['hash_id', "thumbnail_url", "view_url", "download_url"];

    public function getHashIdAttribute()
    {
        return Crypt::encrypt($this->id);
    }

    /**
     * Gets size as string
     * @return string
     */
    public function getSizeAttribute($value)
    {
        try {
            return getSize($value);
        } catch (\Exception $ex) {
            return '0 MB';
        }
    }

    /**
     * Gets file thumbnail url (for inline images, it should return the actual image)
     */
    public function getThumbnailUrlAttribute()
    {

        return (new StorageController())->getThumbnailUrl($this->getOriginal('name'), $this->hash_id);
    }

    /**
     * Url at which attachment can be viewed in full quality
     */
    public function getViewUrlAttribute()
    {
        return Config::get("app.url")."/api/view-attachment/".$this->hash_id;
    }

    /**
     * Url at which attachment can be viewed in full quality
     */
    public function getDownloadUrlAttribute()
    {
        return Config::get("app.url")."/api/download-attachment/".$this->hash_id;
    }

    /**
     * In case of old emails, there won't be any content Id. So we need to give name as contentId so that inline images don't break
     * NOTE: in future versions it can be removed
     * @since v3.1.0
     */
    public function getContentIdAttribute($value)
    {
        return $value ? : $this->name;
    }

    public function getNameAttribute($value)
    {
        return basename($value);
    }
}
