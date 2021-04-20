<?php

namespace App\FaveoStorage\Controllers;

use App\Facades\Attach;
use App\FaveoStorage\Requests\CkEditorRequest;
use App\FaveoStorage\Requests\TinyMceUploadRequest;
use App\Http\Controllers\Controller;
use App\Model\helpdesk\Settings\CommonSettings;
use App\Model\helpdesk\Settings\FileSystemSettings;
use App\Model\helpdesk\Ticket\Ticket_Thread;
use App\Model\helpdesk\Ticket\Ticket_attachments;
use App\Structure\MailAttachment;
use Auth;
use Config;
use Crypt;
use DOMDocument;
use ErrorException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Image;
use Lang;
use Logger;

class StorageController extends Controller
{
    /**
     * storage configuration
     * @param string $option
     * @return string
     */
    protected function settings($option)
    {
        $settings = new CommonSettings();
        $setting  = $settings->getOptionValue('storage', $option);
        $value    = "";
        if ($setting) {
            $value = $setting->option_value;
        }
        return $value;
    }

    /**
     * get the default root
     * @param string $type
     * @return string
     */
    public function root($type = 'private-root')
    {
        $root = $this->settings($type);
        if (!$root && $type == 'private-root') {
            $root = storage_path('app/private');
        }
        if (!$root && $type == 'public-root') {
            $root = public_path();
        }
        $carbon = \Carbon\Carbon::now();
        return $root . DIRECTORY_SEPARATOR . $carbon->year . DIRECTORY_SEPARATOR . $carbon->month . DIRECTORY_SEPARATOR . $carbon->day;
    }

    /**
     * upload attachment file to system
     *
     * NOTE: $filepath is added as a workaround for php-imap package to work and should be removed while rewriting this module
     *
     * @param string $filename
     * @param string $type
     * @param integer $size
     * @param string $disposition
     * @param integer $thread_id
     * @param mixed $attachment
     * @return string
     *
     */
    public function upload($filename, $type, $size, $disposition, $thread_id, $attachment, $recur = false, $realPath = '', $contentId = null, $driver = null)
    {
        $upload = new Ticket_attachments();
        $name   = $upload->whereName($filename)->select('name')->first();

        // if filename ends with `.token` and its size is 0, it should not create that attachment
        if (ends_with($filename, '.token') && !$size) {
            return;
        }

        if ($name) {
            // to avoid files with duplicate names
            $filename = str_random(5) . "_" . $filename;
        }

        $upload->thread_id = $thread_id;
        $upload->name      = $filename;
        $upload->type      = $type;
        $upload->size      = $size;
        $upload->poster    = $disposition;

        // get degaul storage driver
        $upload->driver    = $driver ? : FileSystemSettings::value('disk');

        $upload->content_id    = $contentId;

        $uploadPath  = Attach::getFullPath($realPath, $driver);
        $upload->path = $uploadPath;
        $s            = [
            'pathname'  => Attach::getFullPath($filename, $driver),
            'extension' => $type,
            'filename'  => $filename,
            'size'      => $size,
            'type'      => $type,
            'path'      => $uploadPath
        ];
//        $this->uploadInLocal($attachment, $upload_path, $filename, $realPath);

        if ($recur) {
            return $s;
        }

        $upload->save();

        return $filename;
    }

    /**
     * upload to system
     * @param mixed $attachment
     * @param string $upload_path
     * @param string $filename
     * @param string $realPath
     */
    public function uploadInLocal($attachment, $upload_path, $filename, $realPath)
    {
            if (!\File::exists($upload_path)) {
                \File::makeDirectory($upload_path, 0777, true);
            }
            $path = $upload_path . DIRECTORY_SEPARATOR . $filename;
            if (method_exists($attachment, 'getStructure')) {
                $attachment->saveAs($path);
            }
            else {
                // if it is a recur attachment or batch ticket, it should copy because recur attachments will be required again
                // if it is normal ticket attachment, it should move

                // if $realPath has storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'recur', its a recur attachment
                $recurBasePath = storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'recur';
                $batchBasePath = storage_path().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'batch';

                $isRecurAttachment = strpos($realPath, $recurBasePath) !== false;
                $isBatchAttachment = strpos($realPath, $batchBasePath) !== false;

                if($isRecurAttachment || $isBatchAttachment){
                  return \File::copy($realPath, $upload_path.'/'.$filename);
                }

                return \File::copy($realPath, $upload_path.'/'.$filename);
            }
    }
    /**
     * save attachemnt details to database
     * todo: this method is basically doing nothing but calling `saveReplyAttachment` and could be removed
     * @param integer $thread_id
     * @param array $attachments
     * @param array $inline
     * @return object
     * @throws \Exception
     */
    public function saveAttachments($thread_id, $attachments = [], $inline = [])
    {
        if (is_array($attachments) || is_array($inline)) {
            $ticket_thread = Ticket_Thread::find($thread_id);
            if (!$ticket_thread) {
                throw new \Exception('Thread not found');
            }
            $PhpMailController      = new \App\Http\Controllers\Common\PhpMailController();
            $NotificationController = new \App\Http\Controllers\Common\NotificationController();
            $ticket_controller      = new \App\Http\Controllers\Agent\helpdesk\TicketController($PhpMailController, $NotificationController);
            $thread                 = $ticket_controller->saveReplyAttachment($ticket_thread, $attachments, $inline);
        }
        return $thread;
    }

    /**
     * save attachment/inline document if it is an object
     * @param integer $thread_id
     * @param mixed $attachment
     * @return string
     */
    public function saveObjectAttachments($thread_id, $attachment, $recur = false, $disposition = 'ATTACHMENT', $driver = null)
    {
        if (is_object($attachment)) {
            $disk  = $driver ?: FileSystemSettings::value('disk');

            $uploadPath = "ticket_attachments/" . now()->year . '/' . now()->month . '/' . now()->day;

            $uploadedFileObject = is_a($attachment, MailAttachment::class)
                ? new UploadedFile($attachment->filePath, basename($attachment->filePath), null, 0, false)
                : $attachment;

            $fileName = Attach::put($uploadPath, $uploadedFileObject, $disk, true, false);

            return $this->upload(
                $fileName,
                $uploadedFileObject->getMimeType(),
                $uploadedFileObject->getSize(),
                $disposition,
                $thread_id,
                $attachment,
                $recur,
                $uploadPath,
                !empty($attachment->contentId) ? $attachment->contentId : null,
                $driver
            );
        }
    }

    /**
     * get the attachemnt/inline to render
     * @param string $drive
     * @param string $name
     * @param string $root
     * @return type
     */
    public function getFile($drive, $name, $root)
    {
        if ($drive != "database") {
            $root = $root . "/" . $name;
            if (\File::exists($root)) {
                //chmod($root, 0755);
                return \File::get($root);
            }
        }
    }

    /**
     * get the attachemnt/inline to render
     * @param CkEditorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function ckEditorUpload(CkEditorRequest $request)
    {
        $file = $request->file('upload');

        $file_name = time() . $file->getClientOriginalName();

        $file_path = "ckeditor_attachements"."/".date('Y')."/".date('m')."/";

        $file->move($file_path, $file_name);

        return response()->json(['uploaded' => true,'url' => url($file_path.$file_name)]);
    }

    /**
     * Gets attachment as image or downloaded file
     * @param integer $id   id of the attachment
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function downloadAttachment($id)
    {
        // decrypt id, if invalid decryption then abort
        try {
            $attachment = Ticket_attachments::where('id', '=', Crypt::decrypt($id))->first();

            if (!$attachment && !Attach::exists($attachment->getOriginal('name'), $attachment->driver)) {
                return redirect('404');
            }

            return Attach::download($attachment->getOriginal('name'), $attachment->driver);
        } catch (\Exception $e) {
            return errorResponse(Lang::get('lang.file_not_found'));
        }
    }

    /**
     * Gets attachment as image or downloaded file
     * @param integer $id   id of the attachment
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function viewAttachment($id, $thumbnailMode = false)
    {
        $noDataFoundImagePath = public_path()."/themes/default/common/images/nodatafound.png";

        // decrypt id, if invalid decryption then abort
        try {
            $attachment = Ticket_attachments::where('id', '=', Crypt::decrypt($id))->first();

            if (!$attachment && !Attach::exists($attachment->getOriginal('name'), $attachment->driver)) {
                // return image showing just an attachment icon
                return response()->file($noDataFoundImagePath);
            }

            // if image is inline, we do not need to reduce the quality of the image
            if (!self::isInline($attachment)
                &&
                $this->isThumbnailGeneratable(Attach::getFullPath($attachment->getOriginal('name'), $attachment->driver))
                &&
                $thumbnailMode
            ) {
                return Attach::getThumbnails($attachment->getOriginal('name'), $attachment->driver);
            }
            return Attach::view($attachment->getOriginal('name'), $attachment->driver);
        } catch (\Exception $e) {
            return response()->file($noDataFoundImagePath);
        }
    }

    /**
     * Gets thumbnail for attachments. For inline image, it will return same image with manipulating its quality
     * @param integer $id   hash id of the attachment
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getThumbnail($id)
    {
        return $this->viewAttachment($id, true);
    }

    /**
     * Gets thumbnail URL
     * @param $filePath
     * @param null $hashId
     * @return string
     */
    public function getThumbnailUrl($filePath, $hashId = null)
    {
        if ($this->isThumbnailGeneratable($filePath)) {
            return Config::get("app.url")."/api/thumbnail/".$hashId;
        } else {
            return $this->getThumbnailUrlByMimeType($filePath);
        }
    }

    /**
     * If thumbnail generatable or not
     * @param $filePath
     * @return bool
     */
    private function isThumbnailGeneratable($filePath)
    {
        return in_array(strtolower(pathinfo($filePath, PATHINFO_EXTENSION)), ["png", "jpg","gif", "jpeg", "svg"]);
    }

    /**
     * Gets thumbnail for by file types
     * @param $filePath
     * @return string
     */
    public function getThumbnailUrlByMimeType($filePath)
    {
        $baseIconViaURLPath = Config::get("app.url")."/themes/default/common/images/";

        $baseIconViaFileSystemPath = public_path()."/themes/default/common/images/";

        $mimeType = pathinfo($filePath, PATHINFO_EXTENSION);

        // miraculously we have icons with same name as file extension, so using that logic
        $doesIconExist = file_exists($baseIconViaFileSystemPath.$mimeType.".png");

        $driverOfFilePath = Ticket_attachments::where('name', $filePath)->value('driver');

        $doesFileExist = Attach::exists($filePath, $driverOfFilePath ?: FileSystemSettings::value('disk'));

        if (!$doesFileExist) {
            return $baseIconViaURLPath."/nodatafound.png";
        }

        if ($doesIconExist) {
            return $baseIconViaURLPath."/".$mimeType.".png";
        }

        return $baseIconViaURLPath."/attach.png";
    }

    /**
     * tells if a file is inline
     * @param object $fileObject
     * @return bool
     */
    public static function isInline($fileObject)
    {
        return isset($fileObject->poster) && strtolower($fileObject->poster) == "inline";
    }

    /**
     * Gets thumbnail URL by its path
     * @param $filePath
     * @return string
     */
    public function getThumbnailUrlByPath($filePath)
    {
        if (!$this->isThumbnailGeneratable($filePath)) {
            return $this->getThumbnailUrlByMimeType($filePath);
        }
        return Config::get("app.url")."/api/thumbnail-by-path?path=".$filePath;
    }

    /**
     * Gets thumbnail of a filepath
     * @param Request $request
     * @return mixed|string
     */
    public function getThumbnailByPath(Request $request)
    {
        $filePath = $request->path;

        if (Auth::check() && Auth::user()->role != "user") {
            // if file is a doc, give public icon path else private path
            if ($this->isThumbnailGeneratable($filePath)) {
                return Image::make($filePath)->response(null, 5);
            }
            return $this->getThumbnailUrlByMimeType($filePath);
        }

        return errorResponse(Lang::get("lang.permission_denied"));
    }

    /**
     * This string replaces thumbnail_url from string with contentId of corresponding attachment
     * @param Ticket_Thread $thread
     * @return string
     */
    public function sanitizeThreadForInlineAttachments(Ticket_Thread $thread)
    {
        try {
            $body = $thread->body;
            // scan body for thumbnail_url's and replace it with actual attachment
            $doc = new DOMDocument();

            @$doc->loadHTML($body);

            $images = @$doc->getElementsByTagName('img');

            foreach ($images as $image) {
                try {
                    $src = $image->getAttribute("src");

                    $hashId = str_replace(Config::get("app.url")."/api/thumbnail/", "", $src);

                    $attachment = Ticket_attachments::whereId(\Crypt::decrypt($hashId))->first();

                    $contentId = $attachment->content_id;

                    $body = str_replace($src, "cid:$contentId", $body, $replacementCount);

                    if ($replacementCount) {
                        // creating that as attachment object for this threadId
                        $attachmentForNewTicket = $attachment->replicate();
                        $attachmentForNewTicket->thread_id = $thread->id;
                        $attachmentForNewTicket->saveQuietly();
                    }
                } catch (DecryptException $e) {
                    Logger::exception($e);
                    break;
                }
            }
        } catch (ErrorException $e) {
            // NOTE: there will be cases where there won't be valid HTML coming in mails
            // normally html purifier will make it a valid HTML but just for precaution
        }

        $thread->body = $body;
        $thread->saveQuietly();
    }

    /**
     * Uploads the file copied from tinymce.
     * @param TinyMceUploadRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadHandlerForTinyMce(TinyMceUploadRequest $request): \Illuminate\Http\JsonResponse
    {
        $path = Attach::put('tinymce_attachments', $request->file('file'), null, null, true, 'public');

        return response()->json(['location' => Attach::getUrlForPath($path, null, 'public')]);
    }

}
