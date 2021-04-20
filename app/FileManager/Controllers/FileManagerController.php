<?php

namespace App\FileManager\Controllers;

use App\Facades\Attach;
use App\FileManager\Events\BeforeInitialization;
use App\FileManager\Events\Deleting;
use App\FileManager\Events\DirectoryCreated;
use App\FileManager\Events\DirectoryCreating;
use App\FileManager\Events\DiskSelected;
use App\FileManager\Events\Download;
use App\FileManager\Events\FileCreated;
use App\FileManager\Events\FileCreating;
use App\FileManager\Events\FilesUploaded;
use App\FileManager\Events\FilesUploading;
use App\FileManager\Events\FileUpdate;
use App\FileManager\Events\Paste;
use App\FileManager\Events\Rename;
use App\FileManager\Events\Zip as ZipEvent;
use App\FileManager\Events\Unzip as UnzipEvent;
use App\FileManager\Models\FileManagerAclRule;
use App\FileManager\Requests\FileManagerUploadRequest;
use App\FileManager\Requests\RequestValidator;
use App\FileManager\FileManager;
use App\FileManager\Services\Zip;
use App\Model\helpdesk\Settings\FileSystemSettings;
use Cache;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Storage;

class FileManagerController extends Controller
{
    /**
     * @var FileManager
     */
    public $fm;

    /**
     * FileManagerController constructor.
     *
     * @param FileManager $fm
     */
    public function __construct(FileManager $fm)
    {
        $this->fm = $fm;
    }

    /**
     * Initialize file manager
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function initialize()
    {
        event(new BeforeInitialization());

        return response()->json(
            $this->fm->initialize(request('type'))
        );
    }

    private function getUserDepartments()
    {
        // actually returning departments
        return array_column(\Auth::user()->departments()->get()->toArray(), 'id');
    }

    private function fileExistsInStorageDisk($fileName, $disk)
    {
        $storageAdapter = Storage::disk($disk);

        return Cache::remember("{$fileName}-{$disk}-exists", now()->addMinutes(60), function () use ($disk, $fileName, $storageAdapter) {
            return $storageAdapter->exists($fileName);
        });
    }

    private function getFileMetadata($fileName, $disk)
    {
        $storageAdapter = Storage::disk($disk);

        return Cache::rememberForever("{$fileName}-{$disk}-meta-data", function () use ($fileName, $storageAdapter) {
            return $storageAdapter->getMetadata($fileName);
        });
    }

    /**
     * Get files and directories for the selected path and disk
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function content(RequestValidator $request)
    {
        $perPage = 25; //hard-coding temporarily

//        $page = $request->page ?: 1;

        $disk = $request->disk;

        $fileAclBaseQuery = FileManagerAclRule::where(['disk' => $disk, 'type' => 'file']);

        $showPublicFiles = FileSystemSettings::value('show_public_folder_with_default_disk');

        if (in_array($request->type, ['page', 'kb'])) {
            $filesAndFolders = $fileAclBaseQuery->where(['hidden' => 0])->orderBy('updated_at', 'desc');
        } elseif ($showPublicFiles) {
            $filesAndFolders = $fileAclBaseQuery->orderBy('updated_at', 'desc')->where(['hidden' => 0])
                ->orWhere(function ($query) {
                    $query->where(['type' => 'file','hidden' => 1])->whereHas(
                        'departments',
                        function ($q) {
                            return $q->whereIn('department_id', $this->getUserDepartments());
                        }
                    );
                });
        } else {
            $filesAndFolders = $fileAclBaseQuery->orderBy('updated_at', 'desc')->where('hidden', 1)->whereHas(
                'departments',
                function ($q) {
                    return $q->whereIn('department_id', $this->getUserDepartments());
                }
            );
        }

//        $lastPage = ceil($filesAndFolders->count() / $perPage);
//
//        $startingIndex = ($page *$perPage) - $perPage;
//
//        $total = $filesAndFolders->count();
//
//        $storageAdapter = Storage::disk($disk);

//        $filesInDb = $filesAndFolders->skip($startingIndex)->take($perPage)->get()->toArray();
        $files = $filesAndFolders->paginate($perPage);

        $allFileCollection = $files->getCollection();

        $existingFileCollection = $allFileCollection->filter(function ($file) use ($disk) {
            $pathInfo = pathinfo($file->path);
            $invalidFile = (empty($pathInfo['extension'])  || empty($pathInfo['basename']) || empty($pathInfo['filename']));
            return $this->fileExistsInStorageDisk($file->path, $disk) && !$invalidFile;
        });

        $files->setCollection($existingFileCollection->values());

        $files->getCollection()->transform(function ($item) use ($disk) {

            $metadata = $this->getFileMetadata($item->path, $disk);

            $pathInfo = pathinfo($metadata['path']);

            $metadata['extension'] = $pathInfo['extension'];
            $metadata['dirname'] = (!empty($pathInfo['dirname'])) ? $pathInfo['dirname'] : '';
            $metadata['basename'] = $pathInfo['basename'];
            $metadata['filename'] = $pathInfo['filename'];

            return $metadata;
        });

//        $filesCollection = $files->getCollection();
//
//        $filteredCollection = $filesCollection->filter(function ($item) {
//            return (bool) $item;
//        });
//
//        $files->setCollection($filteredCollection->values());

//        $existingFiles = array_filter(array_column($filesInDb, 'path'), function ($item) use ($disk, $storageAdapter) {
//            return Cache::remember("{$item}-{$disk}-exists", now()->addMinutes(60), function () use ($disk, $item, $storageAdapter) {
//                return $storageAdapter->exists($item);
//            });
//        });
//
//        $files = collect($existingFiles)->map(function ($item) use ($storageAdapter, $disk) {
//            return collect(
//                Cache::rememberForever("{$item}-{$disk}-meta-data", function () use ($item, $storageAdapter) {
//                    return $storageAdapter->getMetadata($item);
//                })
//            );
//        })->map(function ($value) {
//            $pathInfo = pathinfo($value['path']);
//            if (empty($pathInfo['extension']) || empty($pathInfo['dirname']) || empty($pathInfo['basename']) || empty($pathInfo['filename'])) {
//                return false;
//            }
//            $value['extension'] = $pathInfo['extension'];
//            $value['dirname'] = $pathInfo['dirname'];
//            $value['basename'] = $pathInfo['basename'];
//            $value['filename'] = $pathInfo['filename'];
//            return $value;
//        })->filter(function ($value) {
//            return (bool) $value;
//        });

//        $perPageCount = $files->count();
        return $files;
    }

    /**
     * Directory tree
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree(RequestValidator $request)
    {
        return response()->json(
            $this->fm->tree(
                $request->input('disk'),
                $request->input('path'),
                $request->input('type')
            )
        );
    }

    /**
     * Check the selected disk
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectDisk(RequestValidator $request)
    {
        event(new DiskSelected($request->input('disk')));

        return response()->json([
            'result' => [
                'status'  => 'success',
                'message' => 'diskSelected',
            ],
        ]);
    }

    /**
     * Upload files
     *
     * @param FileManagerUploadRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(FileManagerUploadRequest $request)
    {
        event(new FilesUploading($request));

        $uploadResponse = $this->fm->upload(
            $request->input('disk'),
            $request->input('path'),
            $request->file('files'),
            $request->input('overwrite'),
            $request->input('type')
        );

        if (!empty($uploadResponse['result']['path'])) {
            $request->merge(['path' => $uploadResponse['result']['path']]);
        }

        event(new FilesUploaded($request));

        return response()->json($uploadResponse);
    }

    /**
     * Delete files and folders
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(RequestValidator $request)
    {
        event(new Deleting($request));

        $deleteResponse = $this->fm->delete(
            $request->input('disk'),
            $request->input('items')
        );

        return response()->json($deleteResponse);
    }

    /**
     * Copy / Cut files and folders
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paste(RequestValidator $request)
    {
        event(new Paste($request));

        return response()->json(
            $this->fm->paste(
                $request->input('disk'),
                $request->input('path'),
                $request->input('clipboard')
            )
        );
    }

    /**
     * Rename
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function rename(RequestValidator $request)
    {
        event(new Rename($request));

        return response()->json(
            $this->fm->rename(
                $request->input('disk'),
                $request->input('newName'),
                $request->input('oldName')
            )
        );
    }

    /**
     * Download file
     *
     * @param RequestValidator $request
     *
     * @return mixed
     */
    public function download(RequestValidator $request)
    {
        event(new Download($request));

        return $this->fm->download(
            $request->input('disk'),
            $request->input('path')
        );
    }

    /**
     * Create thumbnails
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\Response|mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function thumbnails(RequestValidator $request)
    {
        return $this->fm->thumbnails(
            $request->input('disk'),
            $request->input('path')
        );
    }

    /**
     * Image preview
     *
     * @param RequestValidator $request
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function preview(RequestValidator $request)
    {
        return $this->fm->preview(
            $request->input('disk'),
            $request->input('path')
        );
    }

    /**
     * File url
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function url(RequestValidator $request)
    {
        return response()->json(
            $this->fm->url(
                $request->input('disk'),
                $request->input('path')
            )
        );
    }

    /**
     * Create new directory
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDirectory(RequestValidator $request)
    {
        event(new DirectoryCreating($request));

        $createDirectoryResponse = $this->fm->createDirectory(
            $request->input('disk'),
            $request->input('path'),
            $request->input('name')
        );

        if ($createDirectoryResponse['result']['status'] === 'success') {
            event(new DirectoryCreated($request));
        }

        return response()->json($createDirectoryResponse);
    }

    /**
     * Create new file
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFile(RequestValidator $request)
    {
        event(new FileCreating($request));

        $createFileResponse = $this->fm->createFile(
            $request->input('disk'),
            $request->input('path'),
            $request->input('name')
        );

        if ($createFileResponse['result']['status'] === 'success') {
            event(new FileCreated($request));
        }

        return response()->json($createFileResponse);
    }

    /**
     * Update file
     *
     * @param RequestValidator $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFile(RequestValidator $request)
    {
        event(new FileUpdate($request));

        return response()->json(
            $this->fm->updateFile(
                $request->input('disk'),
                $request->input('path'),
                $request->file('file')
            )
        );
    }

    /**
     * Stream file
     *
     * @param RequestValidator $request
     *
     * @return mixed
     */
    public function streamFile(RequestValidator $request)
    {
        return $this->fm->streamFile(
            $request->input('disk'),
            $request->input('path')
        );
    }

    /**
     * Create zip archive
     *
     * @param RequestValidator $request
     * @param Zip              $zip
     *
     * @return array
     */
    public function zip(RequestValidator $request, Zip $zip)
    {
        event(new ZipEvent($request));

        return $zip->create();
    }

    /**
     * Extract zip archive
     *
     * @param RequestValidator $request
     * @param Zip              $zip
     *
     * @return array
     */
    public function unzip(RequestValidator $request, Zip $zip)
    {
        event(new UnzipEvent($request));

        return $zip->extract();
    }

    /**
     * Integration with ckeditor 4
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ckeditor()
    {
        return view('file-manager::ckeditor');
    }

    /**
     * Integration with TinyMCE v4
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinymce()
    {
        return view('file-manager::tinymce');
    }

    /**
     * Integration with TinyMCE v5
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinymce5()
    {
        return view('file-manager::tinymce5');
    }

    /**
     * Integration with SummerNote
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function summernote()
    {
        return view('file-manager::summernote');
    }

    /**
     * Simple integration with input field
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fmButton()
    {
        return view('file-manager::fmButton');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $filePaths = [];

        $atLeastOneFileNotFound = false;

        list($files, $storageAdapter) = $this->getFilesAndStorageAdapter($request);

        foreach ($files as $file) {
            if ($storageAdapter->missing($file)) {
                $atLeastOneFileNotFound = true;
                continue;
            }

            $fullPath = $storageAdapter->path($file); //path with filename ex: /storage/app/attachments/hello.png

            $path =  strstr($fullPath, $file, true) ?: $fullPath; //path WITHOUT filename ex: /storage/app/attachments/

            $filePaths[] = [
                'filename' => $file,
                'path' =>  $path,
                'size' => $storageAdapter->size($file),
                'type' => pathinfo($file, PATHINFO_EXTENSION),
                'disk' => $request->disk,
                'name' => basename($file)
            ];
        }

        return ($atLeastOneFileNotFound)
            ? errorResponse(trans('filemanager::lang.file-manager-attachment-error-message'))
            : successResponse('', $filePaths);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInlineFileInfo(Request $request): \Illuminate\Http\JsonResponse
    {
        $urls = [];

        $atLeastOneFileNotFound = false;

        list($files, $storageAdapter) = $this->getFilesAndStorageAdapter($request);

        foreach ($files as $file) {
            if ($storageAdapter->missing($file)) {
                $atLeastOneFileNotFound = true;
                continue;
            }

            $urls[] = [
                "name" => basename($file),
                'link' => str_replace(' ', '%20', Attach::getUrlForPath($file, $request->disk, 'public'))
            ];
        }

        return ($atLeastOneFileNotFound)
            ? errorResponse(trans('filemanager::lang.file-manager-attachment-error-message'))
            : successResponse('', $urls);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getFilesAndStorageAdapter(Request $request): array
    {
        $files = json_decode($request->names, true);

        $storageAdapter = Storage::disk($request->disk);

        return array($files, $storageAdapter);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUploadInformationFromPhpIni(): \Illuminate\Http\JsonResponse
    {
        return successResponse('', [
            'maxFileUploadCount' => (int) ini_get('max_file_uploads'),
            'maxPostSize' => parse_size(ini_get('post_max_size')),
            'maxSingleFileSize' => parse_size(ini_get('upload_max_filesize'))
        ]);
    }
}
