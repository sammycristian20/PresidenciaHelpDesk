<?php

namespace App\Model\helpdesk\Agent_panel;

use App\BaseModel;

class Canned extends BaseModel
{
    /* define the table name */

    protected $table = 'canned_response';

    /* Define the fillable fields */
    protected $fillable = ['user_id', 'title', 'message', 'created_at', 'updated_at'];
        
    /**
     * relationship for departments
     */
    public function departments()
    {
        return $this->belongsToMany('App\Model\helpdesk\Agent\Department', 'department_canned_resposne','canned_id','dept_id')->withTimestamps();
    }

    /**
     * Get all linked attachments the canned response 
     */
    public function linkedAttachments()
    {
        return $this->morphMany('\App\Model\Common\LinkedAttachment', 'category');
    }

    /**
     * Get all attachments linked with the canned response 
     */
    public function attachments()
    {
        return $this->hasManyThrough(
            '\App\Model\Common\Attachment',
            '\App\Model\Common\LinkedAttachment',
            'category_id',
            'id',
            'id',
            'attachment_id'
        )->where('category_type', static::class);
    }

    /**
     * accessor mehtod
     * 
     */
    public function getMessageAttribute($value)
    {
        // return $value;
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);
        $string = trim(preg_replace('/\s+/', ' ', $html));
        $content = $this->inlineAttachment($string);
        return $content;
    }
    
    /**
     * @todo
     |======================= Temporary block =======================
     | We need to remove all these code written below this block and
     | implement this functionality in a better and optimize manner 
     |===============================================================
     */

    /**
     *
     *
     */
    public function inlineAttachment($body) {

        $attachments = $this->attachments;
        if ($attachments->count() > 0) {

            foreach ($attachments as $attach) {
                if ($attach->disposition == "INLINE" || $attach->disposition == "inline") {
                    $search = "src=\"".$attach->name;
                    $replace = "src=\"data:$attach->file_type;base64," . $this->getEncodedFile($attach->name, $attach->path, $attach->driver);
                    $b = str_replace($search, $replace, $body);
                    $body = $b;
                }
            }
        }
        return $body;
    }

    public function getEncodedFile($name, $root, $drive)
    {
        if (($drive == "database" || !$drive) && $name && base64_decode($name, true) === false) {
            $name = base64_encode($name);
        }

        if ($drive && $drive !== "database") {
            //when file is not an image, we don't need to send base64 of the file but just name and size
            if ($this->poster == 'ATTACHMENT' || $this->poster == 'attachment') {
                if (mime($this->type) != "image" || $this->type == 'application/octet-stream') {
                    return;
                }
            }

            $storage = new \App\FaveoStorage\Controllers\StorageController();
            $content = $storage->getFile($drive, $name, $root);
            if ($content) {
                $name = base64_encode($content);
                if (mime($this->type) != 'image') {
                    $root = $root . "/" . $name;
                }
            }
        }
        return $name;
    }
}
