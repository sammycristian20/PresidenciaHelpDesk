<?php

namespace App\Helper;

use App\Model\helpdesk\Settings\FileSystemSettings;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class AttachmentHelper
{
    /**
     * Stores the files in default disk
     * @param $directory
     * @param \Illuminate\Http\UploadedFile $contents
     * @param null $disk
     * @param null $uniqueFilename
     * @param bool $uuidFolder
     * @param string $visibility
     * @return false|string
     * @throws \Exception
     */
    public function put($directory, $contents, $disk = null, $uniqueFilename = null, $uuidFolder = true, $visibility = 'private')
    {
        $path = ($uuidFolder) ? $directory . "/" . Str::uuid() : $directory;

        $fileUniqueName =  $uniqueFilename ? time() . '_' . $contents->getClientOriginalName() : $contents->getClientOriginalName();

        return $this->getStorageAdapter($disk)
            ->putFileAs($path, $contents, $fileUniqueName, ['visibility' => $visibility]);
    }

    /**
     * Puts the raw file in storage
     * @param $path
     * @param $contents
     * @param $disk
     * @param $visibility
     * @return bool
     * @throws \Exception
     */
    public function putRaw($path, $contents, $disk, $visibility = 'private'): bool
    {
        return $this->getStorageAdapter($disk)->put($path, $contents, ['visibility' => $visibility]);
    }

    /**
     * @param null $disk
     * @return \Illuminate\Filesystem\FilesystemAdapter
     * @throws \Exception
     */
    private function getStorageAdapter($disk = null): \Illuminate\Filesystem\FilesystemAdapter
    {
        $disk = $disk ?: FileSystemSettings::value('disk');

        if (!$disk) {
            throw new \Exception(trans('lang.attach_helper_no_default_disk'));
        }

        return \Storage::disk($disk);
    }

    /**
     * Delete the `path passed` from the default disk
     * @param $path
     * @return bool
     * @throws \Exception
     */
    public function delete($path): bool
    {
        return $this->getStorageAdapter()->delete($path);
    }

    /**
     * Gets the full pathname to the path passed
     * @param $path
     * @param null $disk
     * @param string $type
     * @param bool $url
     * @return string
     * @throws \Exception
     */
    public function getFullPath($path, $disk = null, $type = 'private', $url = false): string
    {
        $storageAdapter = $this->getStorageAdapter($disk);

        if ($storageAdapter->getDriver()->getAdapter() instanceof AwsS3Adapter && $url) {
            return $this->getUrlForPath($path, $disk, $type);
        }

        return $this->getStorageAdapter($disk)->path($path);
    }

    /**
     * @param $path
     * @param null $disk
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function getUrlForPath($path, $disk = null, $type = 'private'): string
    {
        if (! $path) {
            return '';
        }

        $storageAdapter = $this->getStorageAdapter($disk);

        if ($storageAdapter->getDriver()->getAdapter() instanceof AwsS3Adapter) {
            return $storageAdapter->exists($path)
                ? \Cache::remember("{$path}-{$type}-aws-s3-url", now()->addHours(20), function () use ($path, $storageAdapter, $type) {
                    return ($type === 'private')
                        ? $storageAdapter->temporaryUrl($path, Carbon::now()->addDay())
                        : $storageAdapter->url($path);
                })
                : '';
        }

        return $storageAdapter->exists($path) ? asset($storageAdapter->url($path)) : '';
    }

    /**
     * Generates and caches thumbnails
     * NOTE uses intervention/imagecache package for caching
     * TODO add mentioned PHP package; code is already written for caching
     * @param $path
     * @param null $disk
     * @param null $cacheInMinutes
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getThumbnails($path, $disk = null, $cacheInMinutes = null): \Illuminate\Http\Response
    {
        $storageAdapter = $this->getStorageAdapter($disk);

        if ($cacheInMinutes) {
            $thumbnail = Image::cache(function ($image) use ($disk, $path, $storageAdapter) {
                $image->make($storageAdapter->get($path))->fit(80);
            }, $cacheInMinutes);

            return response()->make($thumbnail, 200, ['Content-Type' => $storageAdapter->mimeType($path)]);
        }

        $thumbnail = Image::make($storageAdapter->get($path))->fit(80);

        return $thumbnail->response();
    }

    /**
     * Downloads File
     * @param $path
     * @param null $disk
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function download($path, $disk = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = (!preg_match('/^[\x20-\x7e]*$/', basename($path)))
            ? Str::ascii(basename($path))
            : basename($path);

        return $this->getStorageAdapter($disk)->download($path, $filename);
    }

    /**
     * Returns the path response to view attachments
     * @param $path
     * @param $disk
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function view($path, $disk): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->getStorageAdapter($disk)->response($path);
    }

    /**
     * Checks the path exists/not
     * @param $path
     * @param $disk
     * @return bool
     * @throws \Exception
     */
    public function exists($path, $disk): bool
    {
        return $this->getStorageAdapter($disk)->exists($path);
    }

    /**
     * Gets the file metadata
     * @param $path
     * @param $disk
     * @return bool
     * @throws \Exception
     */
    public function getMetadata($path, $disk = null)
    {
        return $this->getStorageAdapter($disk)->getMetadata($path);
    }

    /**
     * Gets the size of file present in path
     * @param $path
     * @param null $disk
     * @return false|int
     * @throws \League\Flysystem\FileNotFoundException|\Exception
     */
    public function getSizeOfPath($path, $disk = null)
    {
        return $this->getStorageAdapter($disk)->getSize($path);
    }

    /**
     * Gets the mime type of path
     * @param $path
     * @param null $disk
     * @return false|string
     * @throws \League\Flysystem\FileNotFoundException|\Exception
     */
    public function getMimeTypeOfPath($path, $disk = null)
    {
        return $this->getStorageAdapter($disk)->getMimetype($path);
    }
}
