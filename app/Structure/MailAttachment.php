<?php

namespace App\Structure;

/**
 * NOTE : This is something similar to C's concept of structure. Since we do not have struct datatypes in
 * PHP, so we are using class for the same
 *
 * Files that come from mails can have different object structure. For eg. php-ews has a
 * different file structure than php-imap package.
 * So, this common struture can be followed after the execution of FetchMailController
 * to have less code complexity in create ticket module
 */
class MailAttachment
{
    /**
     * Path where file is currently stored
     * @var string
     */
    public $filePath = null;

    /**
     * Name of the file
     * @var string
     */
    public $fileName = null;

    /**
     * contentId of the attachment which comes along with mail
     * @var string|int
     */
    public $contentId = null;

    /**
     * If file is inline or attachment
     * @var string
     */
    public $disposition = null;

    public $type;

    public $size;

    public $disk;

    /**
     * set filename after sanitizing it
     * @param string $name
     */
    public function setFileName(string $name)
    {
        // temporary attachment path can be added here after sanitizing it
        $this->fileName = $this->getSanitizedFileName($name);
    }

    /**
     * Removes invalid characters(forward slash, backslash) from filename and append random string to it to make it unique
     * @param string $fileName
     * @return string
     */
    private function getSanitizedFileName(string $fileName)
    {
        $afterRemovingForwardSlash = str_replace('/', '_', $fileName);

        return str_replace("\\", "_", $afterRemovingForwardSlash);
    }
}
