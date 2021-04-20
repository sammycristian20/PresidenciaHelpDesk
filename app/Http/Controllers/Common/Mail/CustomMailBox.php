<?php

namespace App\Http\Controllers\Common\Mail;

use App\Facades\Attach;
use App\Model\helpdesk\Settings\FileSystemSettings;
use PhpImap\Mailbox;
use PhpImap\IncomingMail;
use PhpImap\IncomingMailAttachment;
use Exception;


/**
 * This class is a workaround for special characters & encoding in php-imap package and must be removed once that bug is removed
 * from the package
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */
class CustomMailBox extends Mailbox
{
	/**
	 * Call IMAP extension function call wrapped with utf7 args conversion & errors handling
	 *
	 * @param $methodShortName
	 * @param array|string $args
	 * @param bool $prependConnectionAsFirstArg
	 * @param string|null $throwExceptionClass
	 * @return mixed
	 * @throws Exception
	 */
	public function imap($methodShortName, $args = [], $prependConnectionAsFirstArg = true, $throwExceptionClass = Exception::class) {

		if(!is_array($args)) {
			$args = [$args];
		}
		foreach($args as &$arg) {
			if(is_string($arg)) {
				$arg = imap_utf7_encode($arg);
			}
		}

		if($prependConnectionAsFirstArg) {
			array_unshift($args, $this->getImapStream());
		}

		//as a workaround we decode the password field
        if ($methodShortName === 'open') {
            $args[2] = imap_utf7_decode($args[2]);
        }

		imap_errors(); // flush errors
		$result = @call_user_func_array("imap_$methodShortName", $args);

		if(!$result) {
			$errors = imap_errors();
			if($errors) {
				if($throwExceptionClass) {
					throw new $throwExceptionClass("IMAP method imap_$methodShortName() failed with error: " . implode('. ', $errors));
				}
				else {
					return false;
				}
			}
		}

		return $result;
	}


    /**
     * AttachmentDir acts as mere indicator to know which folder to delete after attachments are processed
     * @param string $attachmentsDir
     */
    public function setAttachmentsDir($attachmentsDir)
    {
        $this->attachmentsDir = $attachmentsDir;
    }

	/**
	 * NOTE: It is a workaround by overriding the actual Mailbox method with minor change until
	 * that change is implemented in the actual package
	 */
	protected function initMailPart(IncomingMail $mail, $partStructure, $partNum, $markAsSeen = true,  $emlParse = false) {

		$options = FT_UID;
		if(!$markAsSeen) {
			$options |= FT_PEEK;
		}

		if($partNum) { // don't use ternary operator to optimize memory usage / parsing speed (see http://fabien.potencier.org/the-php-ternary-operator-fast-or-not.html)
			$data = $this->imap('fetchbody', [$mail->id, $partNum, $options]);
		}
		else {
			$data = $this->imap('body', [$mail->id, $options]);
		}

		if($partStructure->encoding == 1) {
			$data = imap_utf8($data);
		}
		elseif($partStructure->encoding == 2) {
			$data = imap_binary($data);
		}
		elseif($partStructure->encoding == 3) {
			$data = preg_replace('~[^a-zA-Z0-9+=/]+~s', '', $data); // https://github.com/barbushin/php-imap/issues/88
			$data = imap_base64($data);
		}
		elseif($partStructure->encoding == 4) {
			$data = quoted_printable_decode($data);
		}

		$params = [];
		if(!empty($partStructure->parameters)) {
			foreach($partStructure->parameters as $param) {
				$params[strtolower($param->attribute)] = $this->decodeMimeStr($param->value);
			}
		}
		if(!empty($partStructure->dparameters)) {
			foreach($partStructure->dparameters as $param) {
				$paramName = strtolower(preg_match('~^(.*?)\*~', $param->attribute, $matches) ? $matches[1] : $param->attribute);
				if(isset($params[$paramName])) {
					$params[$paramName] .= $param->value;
				}
				else {
					$params[$paramName] = $param->value;
				}
			}
		}

		//$isAttachment = $partStructure->ifid || isset($params['filename']) || isset($params['name']);
		//removing $partStructure->ifid from the check for attachment as non-attachement
		//file can also have $partStructure->ifid as non-null
		//follow this PR : https://github.com/barbushin/php-imap/pull/239 for reference
		$isAttachment = isset($params['filename']) || isset($params['name']);

		// ignore contentId on body when mail isn't multipart (https://github.com/barbushin/php-imap/issues/71)
		if(!$partNum && TYPETEXT === $partStructure->type) {
			$isAttachment = false;
		}

		if($isAttachment) {
			$attachmentId = mt_rand() . mt_rand();

			if(empty($params['filename']) && empty($params['name'])) {
				$fileName = $attachmentId . '.' . strtolower($partStructure->subtype);
			}
			else {
				$fileName = !empty($params['filename']) ? $params['filename'] : $params['name'];
				$fileName = $this->decodeMimeStr($fileName, $this->serverEncoding);
				$fileName = $this->decodeRFC2231($fileName, $this->serverEncoding);
			}

			$attachment = new IncomingMailAttachment();
			$attachment->id = $attachmentId;
			$attachment->contentId = $partStructure->ifid ? trim($partStructure->id, " <>") : null;
			$attachment->name = $fileName;
			$attachment->disposition = (isset($partStructure->disposition) ? $partStructure->disposition : null);
            $fileSysName = "temporary_mail_attachments/{$this->attachmentsDir}/"  . preg_replace('~[\\\\/]~', '', $mail->id . '_' . $attachmentId) . "_{$fileName}";

            Attach::putRaw($fileSysName, $data, null, 'public');

            $attachment->filePath = $fileSysName;

			$mail->addAttachment($attachment);
		}
		else {
			if(!empty($params['charset'])) {
				$data = $this->convertStringEncoding($data, $params['charset'], $this->serverEncoding);
			}
			if($partStructure->type == 0 && $data) {
				if(strtolower($partStructure->subtype) == 'plain') {
					$mail->textPlain .= $data;
				}
				else {
					$mail->textHtml .= $data;
				}
			}
			elseif($partStructure->type == 2 && $data) {
				$mail->textPlain .= trim($data);
			}
		}
		if(!empty($partStructure->parts)) {
			foreach($partStructure->parts as $subPartNum => $subPartStructure) {
				if($partStructure->type == 2 && $partStructure->subtype == 'RFC822' && (!isset($partStructure->disposition) || $partStructure->disposition !== "attachment")) {
					$this->initMailPart($mail, $subPartStructure, $partNum, $markAsSeen);
				}
				else {
					$this->initMailPart($mail, $subPartStructure, $partNum . '.' . ($subPartNum + 1), $markAsSeen);
				}
			}
		}
	}

	/**
	 * This function uses imap_search() to perform a search on the mailbox currently opened in the given IMAP stream.
	 * For example, to match all unanswered mails sent by Mom, you'd use: "UNANSWERED FROM mom".
	 *
	 * @param string $criteria See http://php.net/imap_search for a complete list of available criteria
	 * @return array mailsIds (or empty array)
	 */
	public function searchMailbox($criteria = 'ALL', $disableServerEncoding = false) {
		return $this->imap('search', [$criteria, SE_UID]) ?: [];
	}

}
