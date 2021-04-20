<?php


namespace App\Http\Controllers\Common\Mail;


use App\Exceptions\MailConnectionFailureException;
use App\Structure\Mail;
use Exception;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfPathsToElementType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Request\GetItemType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use jamesiarmes\PhpEws\Client;

class ExchangeWebServices extends MailServiceProvider
{
    use \App\Traits\ExchangeWebServices;

    /**
     * Connection instance from mail server
     * @var Client
     */
    private $connection;

    /**
     * Current mail which is getting processed
     * @var Mail
     */
    private $mail;

    /**
     * If connection type is sending or fetching
     * NOTE: only used for checking if connection is valid or not
     * @var string
     */
    private $connectionType = "fetching";

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->getEwsConnection($this->connectionType);
    }

    /**
     * @inheritDoc
     * @throws MailConnectionFailureException
     */
    public function getMessageIds(): ?array
    {
        try{
            $this->connection = $this->getConnection();

            return $this->getMailIdsForEWS($this->connection);
        } catch (Exception $e){

            throw new MailConnectionFailureException("[".$this->emailConfig->email_address."]".$e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getMail(): Mail
    {
        $request = new GetItemType();
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        $request->ItemShape->AdditionalProperties = new NonEmptyArrayOfPathsToElementType();
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();

        $item = new ItemIdType();
        $item->Id = $this->messageId;
        $request->ItemIds->ItemId[] = $item;

        $response = $this->connection->GetItem($request);

        $response_messages = $response->ResponseMessages->GetItemResponseMessage;

        $this->mail = $this->getFormattedMail($response_messages[0]->Items->Message[0]);

        return $this->mail;
    }

    /**
     * @inheritDoc
     */
    public function markAsRead()
    {
        // update mails to read and delete
        $request = new \jamesiarmes\PhpEws\Request\UpdateItemType();
        $request->MessageDisposition = 'SaveOnly';
        $request->ConflictResolution = 'AlwaysOverwrite';
        $request->ItemChanges = array();

        $change = new \jamesiarmes\PhpEws\Type\ItemChangeType();
        $change->ItemId = new \jamesiarmes\PhpEws\Type\ItemIdType();
        $change->ItemId->Id = $this->mail->uid;
        $change->ItemId->ChangeKey = $this->mail->changeKey;

        $field = new \jamesiarmes\PhpEws\Type\SetItemFieldType();
        $field->FieldURI = new \jamesiarmes\PhpEws\Type\PathToUnindexedFieldType();
        $field->FieldURI->FieldURI = 'message:IsRead';
        $field->Message = new \jamesiarmes\PhpEws\Type\MessageType();
        $field->Message->IsRead = true;

        $change->Updates->SetItemField[] = $field;
        $request->ItemChanges[] = $change;
        $this->connection->UpdateItem($request);

        if($this->emailConfig->delete_email){
            $this->deleteByMessageId($this->mail->uid);
        }
    }

    /**
     * Deletes a mail by its message id
     * @param  string $messageId
     * @return void
     */
    private function deleteByMessageId(string $messageId)
    {
        $request = new \jamesiarmes\PhpEws\Request\DeleteItemType();
        $request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
        $request->ItemIds->ItemId = new ItemIdType();
        $request->ItemIds->ItemId->Id = $messageId;

        $request->DeleteType = new \jamesiarmes\PhpEws\Enumeration\DisposalType();
        $request->DeleteType = \jamesiarmes\PhpEws\Enumeration\DisposalType::MOVE_TO_DELETED_ITEMS;

        $this->connection->DeleteItem($request);
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function checkIncomingConnection()
    {
        return $this->checkConnectionForEws("fetching");
    }

    /**
     * Checks if setings for outgoing connection is correct
     * @throws \Exception
     */
    public function checkOutgoingConnection()
    {
        return $this->checkConnectionForEws("sending");
    }

    /**
     * Checks if credentials provided for email settings is correct
     * @param string $connectionType
     * @return bool
     * @throws Exception
     */
    protected function checkConnectionForEws($connectionType = 'fetching')
    {
        if (!extension_loaded('soap')) {
            throw new Exception('SOAP extension is not enabled. Please enable the extension.');
        }
        // it should throw exception which should be handled in parent class
        // try to get IDs of the mail for connection setting
        // if any exception happens in this line, it will be thrown and
        // next line will never return
        $this->connectionType = $connectionType;

        $this->getMailIdsForEWS($this->getConnection());

        return true;
    }

    /**
     * Gets mail in the format which faveo accepts for ticket operations
     * @param $mail
     * @return Mail
     */
    private function getFormattedMail($mail) : Mail
    {
        $faveoMail = new Mail();

        $faveoMail->setSubject($mail->Subject);

        $faveoMail->setFrom([$mail->From->Mailbox->EmailAddress =>  $mail->From->Mailbox->Name]);

        $faveoMail->setTo($this->formatInAssociatedArrayForEWS($mail->ToRecipients));

        $faveoMail->setCc($this->formatInAssociatedArrayForEWS($mail->CcRecipients));

        $faveoMail->setBcc($this->formatInAssociatedArrayForEWS($mail->BccRecipients));

        $faveoMail->setReplyTo($this->getReplyToForEWS($mail));

        $faveoMail->setBody($mail->Body->_, true, $this->getAttachmentsForEWS($mail));

        $faveoMail->setUid($mail->ItemId->Id);

        $faveoMail->setIfAutoResponded($this->getRawHeadersForEWS($mail->InternetMessageHeaders));

        $faveoMail->setReferenceIds($mail->References, $mail->InReplyTo);

        $faveoMail->setMessageId($mail->InternetMessageId);

        /**
         * in EWS change key is used for knowing the current status of the mail. In faveo it will be used to mark the mail as read
         * @see https://stackoverflow.com/questions/150359/what-do-i-use-for-changekey-in-ews-updateitem
         */
        $faveoMail->changeKey = $mail->ItemId->ChangeKey;

        return $faveoMail;
    }
}