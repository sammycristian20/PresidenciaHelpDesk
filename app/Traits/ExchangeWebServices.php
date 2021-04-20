<?php

namespace App\Traits;

use App\Facades\Attach;
use App\Model\helpdesk\Settings\FileSystemSettings;
use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Enumeration\UnindexedFieldURIType;
use jamesiarmes\PhpEws\Type\AndType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\PathToUnindexedFieldType;
use jamesiarmes\PhpEws\Type\RestrictionType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;
use jamesiarmes\PhpEws\Type\ItemIdType;
use jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfInternetHeadersType as EWSHeaders;
use jamesiarmes\PhpEws\Request\GetAttachmentType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfRequestAttachmentIdsType;
use jamesiarmes\PhpEws\Type\RequestAttachmentIdType;
use jamesiarmes\PhpEws\Type\FieldOrderType;
use jamesiarmes\PhpEws\Enumeration\SortDirectionType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfFieldOrdersType;
use jamesiarmes\PhpEws\Request\CreateItemType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use jamesiarmes\PhpEws\Type\MessageType;
use jamesiarmes\PhpEws\Type\SingleRecipientType;
use jamesiarmes\PhpEws\Type\EmailAddressType;
use jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use jamesiarmes\PhpEws\Type\BodyType;
use jamesiarmes\PhpEws\Type\FileAttachmentType;
use jamesiarmes\PhpEws\Request\SendItemType;
use jamesiarmes\PhpEws\Type\TargetFolderIdType;
use App\Structure\MailAttachment;

/**
 * Handles all operations related to Exchange server when connected via EWS
 * NOTE: php-ews package is a poorly written package which requires a lot of code just
 * to connect to the server. For the same reason, this trait will be violating
 * the rules of exceeding method length
 *
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
trait ExchangeWebServices
{

  /**
   * Gets EWS connection
   * @return Client
   */
  protected function getEwsConnection(string $connectionType = 'fetching') : Client
  {
    // Set connection information.
    $host = $connectionType == 'fetching' ? $this->emailConfig->fetching_host : $this->emailConfig->sending_host;
    $username = $this->emailConfig->user_name ? $this->emailConfig->user_name : $this->emailConfig->email_address;
    $password = $this->emailConfig->password;
    // if version is not present, we take version 2016
    $version = $this->emailConfig->version ?: "Exchange2016";

    $client = new Client($host, $username, $password, $version);

    /*
     * NOTE FROM AVINASH: we are looking for certificate path in php.ini file. Once we find, we are attaching
     * that in curl request. If certificates not found, there won't be any additional curl options
     * */
    $certificatePath = ini_get("curl.cainfo");

    if(file_exists($certificatePath)) {
        $client->setCurlOptions([
            CURLOPT_SSL_VERIFYHOST=> $certificatePath,
            CURLOPT_SSL_VERIFYPEER=> $certificatePath
        ]);
    }

    return $client;
  }

    /**
     * Gets mail Ids of last 24 hours
     * @param Client $connection
     * @return array
     */
  protected function getMailIdsForEWS(Client $connection) : ?array
  {
    $request = new FindItemType();

    // all unread mails
    $this->requestBuilderUnreadMessages($request);

    $this->requestBuilderInboxMessages($request);

    $this->requestBuilderSortingMessagesByTime($request);

    return $this->getMessageIdsByRequest($connection, $request);
  }

  /**
   * Formats email object in the required format.
   * @param  ArrayOfRecipientsType $emailObjects
   * @return array        associated array of email => name mapping
   */
  private function formatInAssociatedArrayForEWS(ArrayOfRecipientsType $emailObjects = null) : array
  {
    $formattedEmailObjects = [];

    if($emailObjects){
      // it will either recieve array of email addresses with MailBox class
      // or null
      foreach ($emailObjects->Mailbox as $emailObject) {
        $formattedEmailObjects[$emailObject->EmailAddress] = $emailObject->Name;
      }
    }
    return $formattedEmailObjects;
  }

  /**
   * Converts formatted headers into raw string, so that it can be scanned using one common logic
   * @param EWSHeaders $headers list of all the message headers
   * @return string headers in raw format
   */
  private function getRawHeadersForEWS(EWSHeaders $headers = null) : string
  {
    $rawHeaders = '';
    if($headers){
      foreach ($headers->InternetMessageHeader as $header) {
        $rawHeaders  .= "$header->HeaderName: $header->_\n";
      }
    }

    return $rawHeaders;
  }

    /**
     * gets attachement from a mail and format it in require format
     * @param MessageType $mail
     * @return array
     */
  private function getAttachmentsForEWS(MessageType $mail) : array
  {
    $attachments = $mail->Attachments;
    $connection = $this->getEwsConnection();

    // Build the request to get the attachments.
    $request = new GetAttachmentType();
    $request->AttachmentIds = new NonEmptyArrayOfRequestAttachmentIdsType();

    if($attachments && $attachments->FileAttachment){
      // get attachmentId and get attachment out of it. Store it
      foreach ($attachments->FileAttachment as $attachmentObject) {
        $attachmentId = $attachmentObject->AttachmentId->Id;

        // get attachment type
        $id = new RequestAttachmentIdType();
        $id->Id = $attachmentId;
        $request->AttachmentIds->AttachmentId[] = $id;
      }

      $response = $connection->GetAttachment($request);

      $attachmentResponseMessages = $response->ResponseMessages
          ->GetAttachmentResponseMessage;

      $formattedAttachments = [];

      foreach ($attachmentResponseMessages as $attachmentResponseMessage) {

          $attachments = $attachmentResponseMessage->Attachments
              ->FileAttachment;

          $defaultDisk = FileSystemSettings::value('disk');

          foreach ($attachments as $attachment) {

              $fileName = "temporary_mail_attachments/{$this->tempAttachmentPath}/{$attachment->Name}";

              Attach::putRaw($fileName, $attachment->Content, $defaultDisk);

              $formattedAttachment = new MailAttachment;
              $formattedAttachment->setFileName($attachment->Name);
              $formattedAttachment->filePath = $fileName;
              $formattedAttachment->contentId = $attachment->ContentId;
              $formattedAttachment->disposition = $attachment->IsInline ? 'inline' : 'attachment';
              $formattedAttachment->disk = $defaultDisk;
              $formattedAttachment->type = pathinfo($fileName, PATHINFO_EXTENSION);
              $formattedAttachment->size = \Storage::disk($defaultDisk)->size($fileName);
              $formattedAttachments[] = $formattedAttachment;
          }
      }

      return $formattedAttachments;
    }
    return [];
  }

  private function getReplyToForEWS(MessageType $mail) : array
  {
    if(!$mail->ReplyTo){
      return [$mail->From->Mailbox->EmailAddress => $mail->From->Mailbox->Name];
    }
    // replyTo will always be one, but this package considers that as array
    return [$mail->ReplyTo->Mailbox[0]->EmailAddress => $mail->ReplyTo->Mailbox[0]->Name];
  }

    /**
     * Send mails using EWS APIs
     * @param $toEmail
     * @param $toName
     * @param $subject
     * @param $data
     * @param array $ccEmails
     * @param array $attach
     * @param $reference
     * @return void
     */
  public function sendMailByEWS($toEmail, $toName, $subject, $data, array $ccEmails, array $attach, $reference)
  {
    $ews = $this->getEwsConnection('sending');

    // Create message
    $msg = new MessageType();

    $msg->Subject = $subject;

    $this->addFrom($msg, $this->emailConfig->email_address);

    $this->addTo($msg, $toEmail, $toName);

    $this->addCollaboratorsForEws($msg, $ccEmails);

    // get all inline attachments from thread directly
    // extract attachments from thread if thread is available
    $this->addBodyToEwsMail($msg, $data);

    // if referenceIds are present, append those so that those can come on the same mail as different thread
    $this->addReferences($msg, $reference);

    // Save message as draft
    $msgRequest = new CreateItemType();
    $msgRequest->Items = new NonEmptyArrayOfAllItemsType();
    $msgRequest->Items->Message = $msg;
    $msgRequest->MessageDisposition = 'SaveOnly';
    $msgRequest->MessageDispositionSpecified = true;
    $msgResponse = $ews->CreateItem($msgRequest);

    // change key and id handling
    $msgResponseItems = $msgResponse->ResponseMessages->CreateItemResponseMessage[0]->Items;
    $changeKey = $msgResponseItems->Message[0]->ItemId->ChangeKey;
    $itemId = $msgResponseItems->Message[0]->ItemId->Id;

    $attachmentResponse = $this->createAttachments($ews, $attach, $msgResponseItems->Message[0]->ItemId);

    if($attachmentResponse){
       $attResponseId = $attachmentResponse->ResponseMessages->CreateAttachmentResponseMessage[0]->Attachments->FileAttachment[0]->AttachmentId;
       $changeKey = $attResponseId->RootItemChangeKey;
       $itemId = $attResponseId->RootItemId;
    }

    // Save message id from create attachment response
    $msgItemId = new ItemIdType();
    $msgItemId->ChangeKey = $changeKey;
    $msgItemId->Id = $itemId;

    // Send and save message
    $msgSendRequest = new SendItemType();
    $msgSendRequest->ItemIds = new NonEmptyArrayOfBaseItemIdsType();
    $msgSendRequest->ItemIds->ItemId = $msgItemId;

    // saving to sent items
    $msgSendRequest->SavedItemFolderId = new TargetFolderIdType();
    $sentItemsFolder = new DistinguishedFolderIdType();
    $sentItemsFolder->Id = 'sentitems';
    $msgSendRequest->SavedItemFolderId->DistinguishedFolderId = $sentItemsFolder;
    $msgSendRequest->SaveItemToFolder = true;

    $ews->SendItem($msgSendRequest);
  }

    /**
     * Adds body to the message
     * @param MessageType $message
     * @param string $body
     * @return void
     */
  private function addBodyToEwsMail(MessageType &$message, string $body)
  {
    $message->Body = new BodyType();
    $message->Body->BodyType = BodyTypeType::HTML;
    $message->Body->_ = $body;
  }

  /**
   * Creates attachments in the mail
   * @param  Client $ews
   * @param  array $attachmentObjects
   * @param  string $itemId
   * @return Object|void
   */
  private function createAttachments(&$ews, $attachmentObjects, $itemId)
  {
    if(count($attachmentObjects)){

      $attachments = [];

      foreach ($attachmentObjects as $attachmentObject) {

        // it should be considered as billing attachment
        if(isset($attachmentObject['file_name'])){

          $attachments[] = $this->getAttachmentObjectForEws($attachmentObject['file_name'],
            base64_decode($attachmentObject['data'], true), $attachmentObject['mime'], $attachmentObject['poster']);

        } else {
            $fileContents = \Storage::disk($attachmentObject->driver)->get($attachmentObject->getOriginal('name'));
          $filePath = sys_get_temp_dir() . '/' . str_random() . $attachmentObject->name;
          file_put_contents($filePath, $fileContents);
          $attachments[] = $this->getAttachmentObjectForEws($attachmentObject->name,
          file_get_contents($filePath), mime_content_type($filePath), $attachmentObject->poster, $attachmentObject->content_id);
        }
      }

      // Attach files to message
      $attRequest = new \jamesiarmes\PhpEws\Request\CreateAttachmentType();
      $attRequest->ParentItemId = $itemId;
      $attRequest->Attachments = new \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAttachmentsType();
      $attRequest->Attachments->FileAttachment = $attachments;
      return $ews->CreateAttachment($attRequest);
    }
  }

   /**
    * Request builder for sorting messages by time
    * @param  FindItemType $request
    * @return void
    */
   private function requestBuilderSortingMessagesByTime(FindItemType &$request)
   {
     $order = new FieldOrderType();
     $order->FieldURI = new PathToUnindexedFieldType();
     $order->FieldURI->FieldURI = UnindexedFieldURIType::ITEM_DATE_TIME_RECEIVED;
     $order->Order = SortDirectionType::ASCENDING;
     $request->SortOrder = new NonEmptyArrayOfFieldOrdersType();
     $request->SortOrder->FieldOrder[] = $order;
   }

   /**
    * Builds request for fetching emails from inbox
    * NOTE: move to another trait
    * @param  FindItemType &$request
    * @return void
    */
   private function requestBuilderInboxMessages(FindItemType &$request)
   {
     // get from inbox
     $folder_id = new DistinguishedFolderIdType();
     $folder_id->Id = DistinguishedFolderIdNameType::INBOX;
     $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
     $request->ParentFolderIds->DistinguishedFolderId[] = $folder_id;
   }

   /**
    * Builds request for unread messages from all messages
    * NOTE: move to another trait
    * @param  FindItemType &$request
    * @return void
    */
   private function requestBuilderUnreadMessages(FindItemType &$request)
   {
     $request->Restriction = new RestrictionType();
     $request->Restriction->And = new AndType();
     $request->Restriction->And->IsEqualTo = new \jamesiarmes\PhpEws\Type\IsEqualToType();
     $request->Restriction->And->IsEqualTo->FieldURI = new \jamesiarmes\PhpEws\Type\PathToUnindexedFieldType();
     $request->Restriction->And->IsEqualTo->FieldURI->FieldURI = 'message:IsRead';
     $request->Restriction->And->IsEqualTo->FieldURIOrConstant = new \jamesiarmes\PhpEws\Type\FieldURIOrConstantType();
     $request->Restriction->And->IsEqualTo->FieldURIOrConstant->Constant = new \jamesiarmes\PhpEws\Type\ConstantValueType();
     $request->Restriction->And->IsEqualTo->FieldURIOrConstant->Constant->Value = "false";
   }

   /**
    * Gets message ids by passed request
    * @param  Client       $connection
    * @param  FindItemType $request
    * @return array
    */
   private function getMessageIdsByRequest(Client $connection, FindItemType &$request)
   {
     $response = $connection->FindItem($request);

     $responseMessage = $response->ResponseMessages->FindItemResponseMessage[0];

     // Iterate over the messages that were found, printing the subject for each.
     $items = $responseMessage->RootFolder->Items->Message;
     $itemIds = [];

     foreach ($items as $item) {
         $itemIds[] = $item->ItemId->Id;
     }

     return $itemIds;
   }

   /**
    * Adds from to the message
    * @param MessageType $message
    * @param string      $emailAddress  email address which is to be added to from address
    * @return void
    */
   private function addFrom(MessageType &$message, string $emailAddress)
   {
     $message->From = new SingleRecipientType();
     $message->From->Mailbox = new EmailAddressType();
     $message->From->Mailbox->EmailAddress = $emailAddress;
   }

   /**
    * Adds `to` to the request
    * @param MessageType &$message
    * @param string $toEmail  email of the person to whom mail has to be sent
    * @param string $toName   name of the person to whom mail has to be sent
    * @return void
    */
   private function addTo(MessageType &$message, string $toEmail = '', string $toName = '')
   {
     $recipient = new EmailAddressType();
     $recipient->Name = $toName;
     $recipient->EmailAddress = $toEmail;
     $message->ToRecipients = new ArrayOfRecipientsType();
     $message->ToRecipients->Mailbox[] = $recipient;
   }

   /**
    * Adds collaborators to email
    * @param MessageType $message
    * @param array       $ccEmails array of cc emails
    * @return void
    */
   private function addCollaboratorsForEws(MessageType &$message, array $ccEmails = [])
   {
     $ccObject = new ArrayOfRecipientsType();
     for ($i = 0; $i < count($ccEmails); $i++) {
       $ccObject->Mailbox[] = new EmailAddressType();
       $ccObject->Mailbox[$i]->EmailAddress = $ccEmails[$i];
     }
     $message->CcRecipients = $ccObject;
   }

   /**
    * Adds reference Id to the message
    * @param MessageType $message
    * @param string   $reference
    * @return void
    */
   private function addReferences(MessageType &$message, $reference)
   {
     if($reference){
       $message->InReplyTo = $reference;
       $message->References = $reference;
     }
   }

    /**
     * Makes attachment object for ews
     * @param string $fileName
     * @param binary $file
     * @param string $mime
     * @param string $disposition
     * @param null $contentId
     * @return FileAttachmentType
     */
   private function getAttachmentObjectForEws(string $fileName, $file, string $mime, string $disposition, $contentId = null) : FileAttachmentType
   {
     // it should be considered as billing attachment
     $attachment = new FileAttachmentType();
     $attachment->Content = $file;
     $attachment->Name = $fileName;
     $attachment->ContentType = $mime;
     // not removing old code so that it works with old ticket too
     $attachment->ContentId = $contentId;
     $attachment->IsInline = strtolower($disposition) == 'inline' ? true : false;
     return $attachment;
   }
}
