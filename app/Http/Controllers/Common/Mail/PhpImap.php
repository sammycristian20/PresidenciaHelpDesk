<?php


namespace App\Http\Controllers\Common\Mail;


use App\Exceptions\MailConnectionFailureException;
use App\Http\Controllers\Common\Mail\CustomMailBox as Mailbox;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Structure\Mail;
use App\Structure\MailAttachment;
use Exception;
use PhpImap\IncomingMail;

class PhpImap extends MailServiceProvider
{
    private $encoding = 'UTF-8';

    /**
     * Connection instance from mail server
     * @var Mailbox
     */
    private $connection;

    /**
     * Current mail which is getting processed
     * @var Mail
     */
    private $mail;

    /**
     * @inheritDoc
     * @throws \PhpImap\Exceptions\InvalidParameterException
     */
    public function getConnection()
    {
        $host = $this->emailConfig->fetching_host;
        $driver = $this->emailConfig->fetching_protocol;
        $port = $this->emailConfig->fetching_port;
        $encryption = $this->emailConfig->fetching_encryption == 'none' ? 'notls' : $this->emailConfig->fetching_encryption;
        $certificate = $this->emailConfig->mailbox_protocol ? $this->emailConfig->mailbox_protocol : 'novalidate-cert';

        //has to decide whether we need username or email address
        $username = $this->emailConfig->user_name ? $this->emailConfig->user_name : $this->emailConfig->email_address;

        $password = $this->emailConfig->password;

        if(!$host || !$port || !$driver) {
            throw new \UnexpectedValueException("invalid host, driver or port is given");
        }

        return new Mailbox('{' . "$host:$port/$driver/$encryption/$certificate" . '}INBOX',
            $username, $password, $this->tempAttachmentPath, $this->encoding);
    }

    /**
     * @inheritDoc
     * @throws \PhpImap\Exceptions\InvalidParameterException
     * @throws Exception
     */
    public function getMessageIds(): ?array
    {
        $date = date("d M Y", strToTime("-1 days"));

        try {
            $this->connection = $this->getConnection();

            //gets all unseen messages since last day
            return $this->connection->searchMailbox("SINCE \"$date\" UNSEEN");

        } catch (Exception $e){
            if(strpos($e->getMessage(),'US-ASCII') === false){
                throw new MailConnectionFailureException("[".$this->emailConfig->email_address."]".$e->getMessage());
            }

            //getting connection with US-ASCII encoding
            $this->encoding = 'US-ASCII';
            $this->connection = $this->getConnection();

            return $this->connection->searchMailbox("SINCE \"$date\" UNSEEN");
        }
    }

    /**
     * @inheritDoc
     */
    public function getMail(): Mail
    {
        $mail = $this->connection->getMail($this->messageId, false);

        $this->mail = $this->formatMail($mail);

        return $this->mail;
    }

    /**
     * @inheritDoc
     */
    public function markAsRead()
    {
        // use set setFlag
        $this->connection->markMailAsRead($this->mail->uid);

        if ($this->emailConfig->delete_email) {
            $this->connection->deleteMail($this->mail->uid);
        }
    }

    /**
     * @inheritDoc
     * @throws \PhpImap\Exceptions\InvalidParameterException
     */
    public function checkIncomingConnection()
    {
        return $this->getConnection()->getImapStream();
    }

    /**
     * formats the mail which is passed as an instance of PhpImap\IncomingMail
     * @param IncomingMail $mail
     * @return Mail formatted mail with specified fields like from, to, cc, body, subject etc
     */
    private function formatMail(IncomingMail $mail) : Mail
    {
        $faveoMail = new Mail();

        $faveoMail->setSubject($mail->subject);

        $faveoMail->setFrom([$mail->fromAddress => $mail->fromName]);

        $faveoMail->setTo($mail->to);

        $faveoMail->setCc($mail->cc);

        $faveoMail->setBcc($mail->bcc);

        $faveoMail->setReplyTo($mail->replyTo);

        // sometimes textHtml key can be empty, when mails are sent as plain text
        $faveoMail->setBody( $mail->textHtml ?: $mail->textPlain, (bool)$mail->textHtml,
            $this->getAttachmentsForPhpImap($mail));

        $faveoMail->setUid($mail->id);

        $faveoMail->setIfAutoResponded($mail->headersRaw);

        $faveoMail->setReferenceIds(isset($mail->headers->references) ? $mail->headers->references : '',
            isset($mail->headers->in_reply_to) ? $mail->headers->in_reply_to : '');

        $faveoMail->setMessageId($mail->messageId);

        return $faveoMail;
    }

    /**
     * Gets attachment for php-imap by converting it into array of FaveoMailFile object
     * @param IncomingMail $mail
     * @return array
     */
    private function getAttachmentsForPhpImap(IncomingMail $mail) : array
    {
        $attachments = $mail->getAttachments();

        $formattedAttachments = [];

        $defaultDisk = FileSystemSettings::value('disk');

        foreach ($attachments as $attach) {
            $formattedAttachment = new MailAttachment;
            $formattedAttachment->setFileName($attach->name);
            $formattedAttachment->filePath = $attach->filePath;
            $formattedAttachment->contentId = (string)$attach->contentId;
            $formattedAttachment->disposition = $attach->disposition;
            $formattedAttachment->type = pathinfo($attach->filePath, PATHINFO_EXTENSION);
            $formattedAttachment->size = \Storage::disk($defaultDisk)->size($attach->filePath);
            $formattedAttachment->disk = $defaultDisk;
            array_push($formattedAttachments, $formattedAttachment);
        }
        return $formattedAttachments;
    }
}