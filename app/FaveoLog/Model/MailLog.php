<?php

namespace App\FaveoLog\Model;

use App\BaseModel;
use App\Model\helpdesk\Ticket\EmailThread;

class MailLog extends BaseModel
{
    protected $table = 'mail_logs';

    protected $fillable = [

        /**
         * Foriegn key for log category
         */
        'log_category_id',

        /**
         * The person to whom we are sending the mail
         */
        'sender_mail',

        /**
         * The person who recieves the mail
         */
        'reciever_mail',

        /**
         * The referee id from which log has come (ticket or user or mail)
         */
        'referee_id',

        /**
         * The referee type from which log has come (ticket or user or mail)
         */
        'referee_type',

        /**
         * Subject of the mail
         */
        'subject',

        /**
         * Body of the mail
         */
        'body',

        /**
         * Source from which it was generated (mail, web, recur)
         */
        'source',

        /**
         * Status of the mail `QUEUED`, `SENT`, `ACCEPTED`, `REJECTED`
         */
        'status',

        /**
         * CCs and BCCs will be added to it in comma separated form
         */
        'collaborators',

        /**
         * Exception log id
         */
        'exception_log_id',
    ];

    /**
     * Will morph to ticket table, user table
     */
    public function reference()
    {
        return $this->morphTo();
    }

    public function category()
    {
        return $this->belongsTo('App\FaveoLog\Model\LogCategory', 'log_category_id');
    }

    public function getSubjectAttribute($value)
    {
        // in case of empty subject, it should show (no subject)
        if (!$value) {
            return "(no subject)";
        }
        return $value;
    }

    public function getCollaboratorsAttribute($value)
    {
        if(!$value){
            return [];
        }
        return json_decode($value);
    }

    public function setCollaboratorsAttribute($value)
    {
        if(!is_array($value)){
            $value = [];
        }
        $this->attributes["collaborators"] = json_encode($value);
    }

    public function exception()
    {
        return $this->belongsTo('App\FaveoLog\Model\ExceptionLog','exception_log_id');
    }
}
