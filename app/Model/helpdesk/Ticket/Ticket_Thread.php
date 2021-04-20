<?php

namespace App\Model\helpdesk\Ticket;

use App\BaseModel;
use App\FaveoStorage\Controllers\StorageController;
use App\Http\Controllers\Common\TicketsWrite\SlaEnforcer;
use App\Traits\Observable;
use File;

class Ticket_Thread extends BaseModel {

    use Observable;

    protected $table = 'ticket_thread';

    protected $fillable = [
        'id', 'ticket_id', 'staff_id', 'user_id', 'thread_type', 'poster', 'source', 'is_internal', 'title', 'body', 'format', 'ip_address', 'created_at', 'updated_at', 'response_time',
    ];

    /**
     * Attributes which requires html purification. All attributes which allows HTML should be added to it
     */
    protected $htmlAble = ['body'];

    public $notify = true;

    public $send = true;

    public function attach()
    {
        return $this->hasMany('App\Model\helpdesk\Ticket\Ticket_attachments', 'thread_id');
    }

    public function getTitleAttribute($value)
    {
        return utfEncoding(str_replace('"', "'", $value));
    }

    public function getBodyAttribute($value)
    {
        return $this->inlineAttachment($value);
    }

    public function setTitleAttribute($value)
    {
        if ($value == "") {
            $this->attributes['title'] = 'No available';
        } else {
            $this->attributes['title'] = $value;
        }
    }

    public function inlineAttachment($body) {

        foreach ($this->attach as $attachment) {

            if (strtolower($attachment->poster) == "inline") {
                $search = $attachment->name;
                $urlEncodedSearch = rawurlencode($attachment->name);
                $contentIdString = "cid:".$attachment->content_id;

                // doing a contentId replacement for tickets after version v3.1.0
                $body = str_replace($contentIdString, $attachment->thumbnail_url, $body);

                // NOTE FROM AVINASH: htmlpurifier converts "src=some_url" into a valid url by url-encoding it
                // this is a temporary fix. It will be fixed permanently during storageController rewrite
                // doing a normal replace once (for new old ticket compatibility)
                $body = str_replace($search, $attachment->thumbnail_url, $body);

                // doing a urlencode replace once (for new ticket compatibility) bw version 2.3.0 to 3.0.3 for images that has special characters in it
                $body = str_replace($urlEncodedSearch, $attachment->thumbnail_url, $body);
            }
        }
        return $body;
    }

    public function labels($ticketid)
    {
        $label = new \App\Model\helpdesk\Filters\Label();
        return $label->assignedLabels($ticketid);
    }

    public function user()
    {
        $related = 'App\User';
        $foreignKey = 'user_id';
        return $this->belongsTo($related, $foreignKey);
    }

    public function save(array $options = array())
    {
        $changed = $this->isDirty() ? $this->getDirty() : false;
        $thread_ticket = $this->where('ticket_id', $this->attributes['ticket_id'])->select('id')->first();
        if ($thread_ticket) {
            $this->saveThreadType();
        }
        $id = $this->id;
        $model = $this->find($id);
        $save = parent::save($options);
        if ($this->notify) {
            $ids = $this->id;
            $table = $this->find($ids);
            if ($table && $table->is_internal == 1 && $table->thread_type == 'note') {
                $changed = ['note' => $this->body];
                $model = $table;
            }
            $array = ['changes' => $changed, 'model' => $model,'send_mail'=>  $this->send];
            \Event::dispatch('notification-saved', [$array]);
        }
        return $save;
    }

    public function saveThreadType()
    {
        $ticketid = $this->attributes['ticket_id'];
        $thread = $this->where('ticket_id', $ticketid)
                ->where('is_internal', '!=', 1)
                ->where('thread_type', 'first_reply')
                ->where('poster', 'support')
                ->where('title', "")
                ->select('id')
                ->first();
        if (!$thread && checkArray('is_internal', $this->attributes) !== 1 && $this->poster == 'support') {
            $this->attributes['thread_type'] = "first_reply";
        }
    }

    /**
     * NOTE FROM AVINASH: not sure if it is still in use
     * @depreciated as it is mapping to only one rating for a thread where there can be multiple
     */
    public function rating()
    {
        $related = 'App\Model\helpdesk\Ratings\RatingRef';
        $foreignKey = 'thread_id';
        return $this->hasOne($related, $foreignKey);
    }

    /**
     * returns rating object
     */
    public function ratings()
    {
        return $this->hasMany('App\Model\helpdesk\Ratings\RatingRef', 'thread_id');
    }

    public function setUserIdAttributes($value)
    {
        $this->attributes['user_id'] = $value ? : null;
    }

    public function emailThread()
    {
        $related = 'App\Model\helpdesk\Ticket\EmailThread';
        $foreignKey = 'thread_id';
        return $this->hasMany($related, $foreignKey);
    }

    public function ticket()
    {
        return $this->belongsTo('App\Model\helpdesk\Ticket\Tickets','ticket_id');
    }


    public function purify($inline = true, $mail = "")
    {
        return $this->attributes['body'];
        // NOTE FROM AVINASH: this is not needed after htmlpurifier but I am not sure if removing this breaks anything. So just commenting (since v3.1.0)
        //        $str = str_replace("'", '&#039;', $value);
//        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $str);
//        $string = trim(preg_replace('/\s+/', ' ', nl2br($html)));
//        return $string;
    }

    /**
     * Triggers at the end of model activity
     * @param Ticket_Thread $thread
     */
    public function afterSave(Ticket_Thread $thread)
    {
        // updating ticket updated_at column, so that ticket events can be triggered
        // NOTE: using touchable has issues with deleted hook. It gets triggered in deleted hook instead of deleted hook
        // saving updated_at column of related model if any activity happens on this model
        if ($thread->ticket && !$thread->is_internal) {
            $this->updateAvgResponseTime();
            (new SlaEnforcer($this->ticket))->handleSlaRelatedUpdates();

            // sanitizing thread body so that any URL in the body can be replaced by its contentId
            (new StorageController())->sanitizeThreadForInlineAttachments($thread);
        }

    }

    /**
     * Updates avg response time for a ticket
     * @param int $ticketId
     */
    private function updateAvgResponseTime()
    {
        // updating tickets avg_response_time
        // NOTE: not using model since using model will cause additional event to be fired, which is not needed
        $thread = Ticket_Thread::where('ticket_id', $this->ticket_id)
            ->where('poster', 'support')
            ->where('is_internal', 0)
            ->select(\DB::raw('ROUND(AVG(response_time)) as avg_response_time'), "ticket_id")->groupBy('ticket_id')
            ->first();


        // using DB facade to avoid unnecessary call to ticketObserver which in change will call to SlaEnforcer
        if($thread && $thread->ticket){
            \DB::table("tickets")->where("id", $this->ticket_id)->update(['average_response_time'=> $thread->avg_response_time]);
        }
    }

    public function getPdfAbleBodyAttribute()
    {
        try {
            // $this->body
            $body = $this->body;

            // scan body for thumbnail_url's and replace it with actual attachment
            $doc = new \DOMDocument();

            @$doc->loadHTML($body);
            $images = @$doc->getElementsByTagName('img');

            foreach ($images as $image) {

                $src = $image->getAttribute("src");

                try{
                    if(getimagesize($src)){
                        $body = str_replace($src, $this->getRemoteImageAsBase64($src), $body);
                    }
                } catch(\Throwable $e){
                    // do nothing so that rest if the images can be fetched, if one image is not found
                }
            }
            return $body;
        } catch (\Throwable $e){
            return $body;
        }
    }

    /**
     * Gets image from remote server
     * NOTE: it slows down the performance, so should be used only in absolute necessity. Currently its been used in generating PDFs
     * @param string $src
     * @return string
     * @throws \Exception
     * @author avinash kumar <avinash.kumar@ladybirdweb.com>
     */
    protected function getRemoteImageAsBase64(string $src)
    {
        if(getimagesize($src)){
            return "data:image/jpg;base64,".base64_encode(file_get_contents($src));
        }

        throw new \Exception("Invalid image url given");
    }

    public function getSubject() {
        $subject = $this->attributes['title'];
        $array = imap_mime_header_decode($subject);
        $title = "";
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $text) {
                $title .= $text->text;
            }
            return wordwrap($title, 70, "<br>\n");
        }
        return wordwrap($subject, 70, "<br>\n");
    }

}
