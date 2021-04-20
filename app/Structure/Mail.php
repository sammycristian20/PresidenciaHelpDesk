<?php


namespace App\Structure;

use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Structure of mail which faveo accepts.
 * If anyone wants to write a service provider for mail fetching, they only have to provide this structure to faveo to handle ticket creation
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class Mail
{
    /**
     * UID of the mail
     * @var string
     */
    public $uid;

    /**
     * Subject of the mail
     * @var string
     */
    public $subject = "";

    /**
     * the person from which mail is coming from
     * @var array
     */
    public $from;

    /**
     * The people to which mail is addressed
     * @var array
     */
    public $to = [];

    /**
     * The people cc'ed in the mail
     * @var array
     */
    public $cc = [];

    /**
     * the emails which are bcc'ed
     * @see https://en.wikipedia.org/wiki/Blind_carbon_copy
     * @var array
     */
    public $bcc = [];

    /**
     * the email id which is added as reply_to
     * @see https://help.returnpath.com/hc/en-us/articles/220568427-What-is-a-Reply-To-address-
     * @var string
     */
    public $replyTo;

    /**
     * Body of the mail
     * @var string
     */
    public $body;

    /**
     * Raw body of the mail, without any trimming
     * @var string
     */
    public $rawBody;

    /**
     * Attachment objects
     * @var array
     */
    public $attachments = [];

    /**
     * inline images in the mail
     * @var array
     */
    public $inlines = [];

    /**
     * Reference Ids in the mail
     * @see https://www.123formbuilder.com/docs/what-is-the-reference-id/
     * @var array
     */
    public $referenceIds = [];

    /**
     * if mail is auto-responded
     * @var bool
     */
    public $ifAutoResponded = false;

    /**
     * Unique message id
     * @var string
     */
    public $messageId;

    /**
     * Strings which will filter out a mail as auto-responded it is found in mail headers
     */
    const AUTO_RESPONDED_NEEDLES = ['auto-submitted: auto-replied', 'x-autorespond','x-autoreply','auto-submitted: auto-generated'];

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * Setter for `subject`
     * NOTE : can be null is case of different service providers
     * @param null $subject
     */
    public function setSubject($subject = null)
    {
        $this->subject = (string)$subject;
    }

    /**
     * Setter for `from`
     * Incoming value should be a key value pair of email and name ["from@address.com" => "From name"]
     * @param array $value
     */
    public function setFrom(array $value)
    {
        if(count($value) != 1){
            throw new InvalidArgumentException("[Subject::".$this->subject."]"." from field is expected to be a key value pair but received value is ". json_encode($value));
        }

        if($this->sanitizeKeyValuePair($value)) {
            // key value pair
            $this->from = $value;
        }
    }

    /**
     * Setter for `to`
     * should be a [key => value] pair of email and mail
     * NOTE : can be null is case of different service providers
     * @param array|null $value
     * @throws InvalidArgumentException
     */
    public function setTo(array $value = null)
    {
        if($this->sanitizeKeyValuePair($value)){
            $this->to = (array) $value;
        }
    }

    /**
     * Setter for `cc`
     * NOTE : can be null is case of different service providers
     * @param array|null $value
     */
    public function setCc(array $value = null)
    {
        if($this->sanitizeKeyValuePair($value)) {
            $this->cc = (array)$value;
        }
    }

    /**
     * Setter for `cc`
     * NOTE : can be null is case of different service providers
     * @param array|null $value
     */
    public function setBcc(array $value = null)
    {
        if($this->sanitizeKeyValuePair($value)) {
            $this->bcc = (array)$value;
        }
    }

    /**
     * Setter for `replyTo`. It should be key value pair of email and name
     * NOTE : can be null is case of different service providers
     * @param array|null $value
     */
    public function setReplyTo(array $value = null)
    {
        if($this->sanitizeKeyValuePair($value)) {
            $this->replyTo = (array)$value;
        }
    }

    /**
     * Setter for `body`
     * @param string|null $body
     * @param bool $isHtml
     * @param array $attachments
     */
    public function setBody(?string $body, bool $isHtml, array $attachments)
    {
        $this->rawBody = $body;

        // handle email trimming here
        $this->body = $this->getSanitizedMailBody($this->trimBody((string)$body, $attachments), $isHtml);

        $this->setAttachments($attachments);
    }

    /**
     * Filters reply content from rest of the body
     * NOTE: This algorithm must be made very strong. Currently the older method is user as it is
     *
     * @param string $body HTML body
     * @param array $attachments
     * @return string Filtered body after removing older mail content (which gets created while replying)
     */
    private function trimBody(string $body, array &$attachments): string
    {
        $bodyArray = explode('<div style="display:none">---Reply above this line(to trim old messages)--- </div>', $body);

        if(!isset($bodyArray[1])){
            return $body;
        }

        /**
         * PROBLEM: when a reply arrives on a particular mail having an inline image,
         * it will be present in attachment array, but after trimming, it will no
         * longer remain an inline image, so it will be considered as attachment.
         * to avoid this we are removing attachments which were part of stripped body.
         *
         * WARNING: poster property from mail server is not reliable. It can send an inline
         * image as attachment or vice-versa. Try not to use poster for this logic
         */

        if($strippedString = $bodyArray[1]){
            // remove whatever inline images are mentioned in the body from attachment array
            foreach ($attachments as $index => $attachment){
                if(isset($attachment->contentId) && strpos($strippedString, "cid:$attachment->contentId")){
                    unset($attachments[$index]);
                }
            }
        }

        return $bodyArray[0];
    }

    /**
     * Strips all new lines if mail is html
     * REASON : If HTML mail has \n into it, it will be regarded as another new line which causes one extra new line
     * if kept outside div
     * @param string $body
     * @param bool   $isHtml if body is html or not(plain text)
     * @return string
     */
    private function getSanitizedMailBody(string $body = null, bool $isHtml = true) : string
    {
        //sometimes body comes as null, converting that into empty string
        $body = (string)$body;

        if($isHtml){
            $body = preg_replace( "/\r|\n/", "", $body);
        } else {
            $body = nl2br($body);
        }

        return $body;
    }

    /**
     * Sets inline images and attached files into inlines and attachments property
     * @param array $attachments
     */
    public function setAttachments(array $attachments)
    {
        foreach ($attachments as $attachment){
            if(!($attachment instanceof MailAttachment)) {
                throw new \InvalidArgumentException("Passed attachment must be an instance is \App\Structure\MailAttachment::class");
            }

            // scan the body and look for contentId matching contentId in the inline attachment,
            // if not found, remove that file
            $isReferencePresentInBody = $attachment->contentId && strpos($this->body, "cid:$attachment->contentId") !== false;

            $attachmentArray = [
                'filename' => $attachment->filePath,
                'path' =>  $attachment->filePath,
                'size' => $attachment->size,
                'type' => $attachment->size,
                'disk' => $attachment->disk,
                'contentId' => $attachment->contentId
            ];

            if($isReferencePresentInBody){
                $attachment->disposition = "inline";
                $this->inlines[]  = $attachmentArray;
            } else {
                $this->attachments[] = $attachmentArray;
            }
        }
    }

    /**
     * Sets reference Ids for the mail
     * @param string|null $referenceIdString
     * @param string|null $inReplyToString
     */
    public function setReferenceIds(string $referenceIdString = null, string $inReplyToString = null)
    {
        $this->referenceIds = $this->getReferenceIds($referenceIdString, $inReplyToString);
    }

    /**
     * Returns array of reference_ids by merging reference array and reply  In-Reply-To
     * NOTE: some mail servers (for eg) rediffmail doesn't send reference_id at all but instead
     * sends in_reply_to
     * @param string|null $referenceIdString
     * @param string|null $inReplyToString
     * @return array
     */
    private function getReferenceIds(string $referenceIdString = null, string $inReplyToString = null) : array
    {
        $formattedReferenceIds = [];

        if($referenceIdString){
            $formattedReferenceIds = explode(' ', str_replace(['<', '>'], '', $referenceIdString));
        }

        if($inReplyToString){
            $inReplyTo = str_replace(['<', '>'], '', $inReplyToString);
            // in reply_to key, rediffmail has this issue that it sends ' ' instead of +,
            // so this workaround to tackle that
            $formattedReferenceIds[] = $this->replaceSpaceWithPlus($inReplyTo);
        }

        return $formattedReferenceIds;
    }

    /**
     * Replaces spaces with plus (This is a workaround for rediffmail)
     * NOTE: rediffmail referenceId gives empty space(` `) in place of a plus (`+`)
     * @param string $string
     * @return string
     */
    private function replaceSpaceWithPlus(string $string)
    {
        return str_replace(" ","+", $string);
    }

    /**
     * Sets raw header and also decides if mail is auto-responded or not
     * @see https://whatismyipaddress.com/email-header
     * @param $headersRaw
     */
    public function setIfAutoResponded($headersRaw)
    {
        // convert all headers to lowercase
        $headersRaw = strtolower($headersRaw);

        //text that are found in auto replied mail till now. More text can be added to it to block auto responded mail
        foreach (self::AUTO_RESPONDED_NEEDLES as $autoRepliedString) {
            if (strpos($headersRaw, $autoRepliedString) !== false) {
                $this->ifAutoResponded = true;
            }
        }
    }

    /**
     * Sets message id of the mail
     * @param $messageId
     */
    public function setMessageId(string $messageId = null)
    {
        // NOTE FROM AVINASH: message Id can be null in few cases(old mail servers)
        $this->messageId = str_replace(['<', '>'], '', (string)$messageId);
    }

    /**
     * Validates if key value pair is of value email and name
     * @param array|null $value
     * @return bool
     * @throws InvalidArgumentException
     */
    private function sanitizeKeyValuePair(array &$value = null)
    {
        if(!$value){
            return true;
        }

        $sanitizedValue = [];

        foreach ($value as $email => $name){

            $sanitizedEmail = trim($email, "'\"");
            $sanitizedName = trim($name, "'\"");
            $sanitizedValue[$sanitizedEmail] = $sanitizedName;
        }

        // assigning sanitized value back to original variable
        $value = $sanitizedValue;

        return true;
    }

    /**
     * Gets reply to address. If not present, gives `from` address
     * @return mixed
     */
    public function getReplyToAddress()
    {
        $replyTo = $this->replyTo ? : $this->from;

        if(!$replyTo){
            throw new UnexpectedValueException("`From` and `reply_to` are absent. At least one of the values must be there for it to work");
        }

        return array_keys($replyTo)[0];
    }

    /**
     * Gets reply to address. If not present, gives `from` name
     * @return mixed
     */
    public function getReplyToName()
    {
        $replyTo = $this->replyTo ? : $this->from;

        if(!$replyTo){
            throw new UnexpectedValueException("`From` and `reply_to` are absent. At least one of the values must be there for it to work");
        }

        return array_values($replyTo)[0];
    }
}
