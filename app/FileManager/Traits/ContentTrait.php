<?php

namespace App\FileManager\Traits;

use App\FileManager\Models\FileManagerAclRule;
use App\FileManager\Services\ACLService\ACL;
use App\Helper\CollectionHelper;
use App\Model\helpdesk\Settings\FileSystemSettings;
use Cache;
use Illuminate\Support\Arr;
use Storage;

trait ContentTrait
{

    /**
     * Get content for the selected disk and path
     *
     * @param       $disk
     * @param null $path
     *
     * @param string $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getContent($disk, $path = null, $page = 'reply')
    {
        $storageAdapter = Storage::disk($disk);

        $contents = collect($this->getFilteredContents($page, $disk))
            ->map(function ($item) use ($storageAdapter, $disk) {
                return collect(
                    Cache::rememberForever("{$item}-{$disk}-meta-data", function () use ($item, $storageAdapter) {
                        return $storageAdapter->getMetadata($item);
                    })
                );
            })
            ->map(function ($value) {
                $pathInfo = pathinfo($value['path']);
                if (empty($pathInfo['extension']) || empty($pathInfo['dirname']) || empty($pathInfo['basename']) || empty($pathInfo['filename'])) {
                    return;
                }
                $value['extension'] = $pathInfo['extension'];
                $value['dirname'] = $pathInfo['dirname'];
                $value['basename'] = $pathInfo['basename'];
                $value['filename'] = $pathInfo['filename'];
                return $value;
            })->filter(function ($value) {
                return (bool) $value;
            });
        
        // get a list of directories
        $directories = [];

        // get a list of files
        $files = $this->filterFile($disk, $contents->toArray(), $page);

        $files = $this->filterFile($disk, $contents->toArray(), $page);

        return CollectionHelper::paginate(collect($files), 25);
    }

    /**
     * Get directories with properties
     *
     * @param       $disk
     * @param null $path
     * @param string $page
     * @return array
     */
    public function directoriesWithProperties($disk, $path = null, $page = 'reply')
    {
        $contentsToReturn = $this->getFilteredContents($page, $disk, Storage::disk($disk)->listContents($path));

        return $this->filterDir($disk, $contentsToReturn);
    }

    /**
     * Get files with properties
     *
     * @param       $disk
     * @param  null $path
     *
     * @return array
     */
    public function filesWithProperties($disk, $path = null)
    {
        $content = Storage::disk($disk)->listContents($path);

        return $this->filterFile($disk, $content);
    }

    /**
     * Get directories for tree module
     *
     * @param $disk
     * @param $path
     *
     * @return array
     */
    public function getDirectoriesTree($disk, $path = null, $page = 'reply')
    {
        $directories = $this->directoriesWithProperties($disk, $path, $page);

        foreach ($directories as $index => $dir) {
            $directories[$index]['props'] = [
                'hasSubdirectories' => Storage::disk($disk)
                    ->directories($dir['path']) ? true : false,
            ];
        }

        return $directories;
    }

    /**
     * File properties
     *
     * @param       $disk
     * @param  null $path
     *
     * @return mixed
     */
    public function fileProperties($disk, $path = null)
    {
        $file = Storage::disk($disk)->getMetadata($path);

        $pathInfo = pathinfo($path);

        $file['basename'] = $pathInfo['basename'];
        $file['dirname'] = $pathInfo['dirname'] === '.' ? ''
            : $pathInfo['dirname'];
        $file['extension'] = isset($pathInfo['extension'])
            ? $pathInfo['extension'] : '';
        $file['filename'] = $pathInfo['filename'];

        // if ACL ON
        if ($this->configRepository->getAcl()) {
            return $this->aclFilter($disk, [$file])[0];
        }

        return $file;
    }

    /**
     * Get properties for the selected directory
     *
     * @param       $disk
     * @param  null $path
     *
     * @return array|false
     */
    public function directoryProperties($disk, $path = null)
    {
        $directory = Storage::disk($disk)->getMetadata($path);

        $pathInfo = pathinfo($path);

        /**
         * S3 didn't return metadata for directories
         */
        if (!$directory) {
            $directory['path'] = $path;
            $directory['type'] = 'dir';
        }

        $directory['basename'] = $pathInfo['basename'];
        $directory['dirname'] = $pathInfo['dirname'] === '.' ? ''
            : $pathInfo['dirname'];

        // if ACL ON
//        if ($this->configRepository->getAcl()) {
//            return $this->aclFilter($disk, [$directory])[0];
//        }

        return $directory;
    }

    /**
     * Get only directories
     *
     * @param $disk
     * @param $content
     *
     * @param string $page
     * @return array
     */
    protected function filterDir($disk, $content, $page = 'reply')
    {
        // select only dir
        $dirsList = Arr::where($content, function ($item) {
            return $item['type'] === 'dir';
        });

        // remove 'filename' param
        $dirs = array_map(function ($item) {
            return Arr::except($item, ['filename']);
        }, $dirsList);

        return array_values($dirs);
    }

    /**
     * Get only files
     *
     * @param $disk
     * @param $content
     *
     * @param string $page
     * @return array
     */
    protected function filterFile($disk, $content, $page = 'reply')
    {
        // select only files
        $files = Arr::where($content, function ($item) {
            return $item['type'] === 'file';
        });

        return array_values($files);
    }

    /**
     * ACL filter
     *
     * @param $disk
     * @param $content
     *
     * @return mixed
     */
    protected function aclFilter($disk, $content)
    {
        $acl = resolve(ACL::class);

        $withAccess = array_map(function ($item) use ($acl, $disk) {
            // add acl access level
            $item['acl'] = $acl->getAccessLevel($disk, $item['path']);

            return $item;
        }, $content);

        // filter files and folders
        if ($this->configRepository->getAclHideFromFM()) {
            return array_filter($withAccess, function ($item) {
                return $item['acl'] !== 0;
            });
        }

        return $withAccess;
    }

    /**
     * @param string $page
     * @param $disk
     * @param array $content
     * @return array
     */
    private function getFilteredContents(string $page, $disk, array $content = []): array
    {
        $fileAclBaseQuery = FileManagerAclRule::where(['disk' => $disk, 'type' => 'file']);

        $showPublicFiles = FileSystemSettings::value('show_public_folder_with_default_disk');

        if (in_array($page, ['page', 'kb'])) {
            $filesAndFolders = $fileAclBaseQuery->where(['hidden' => 0])->orderBy('updated_at', 'desc')->get()->toArray();
        } elseif ($showPublicFiles) {
            $filesAndFolders = $fileAclBaseQuery->orderBy('updated_at', 'desc')->where(['hidden' => 0])
                ->orWhere(function ($query) {
                    $query->where(['type' => 'file','hidden' => 1])->whereHas(
                        'departments',
                        function ($q) {
                            return $q->whereIn('department_id', $this->getUserDepartments());
                        }
                    );
                })->get()->toArray();
        } else {
            $filesAndFolders = $fileAclBaseQuery->orderBy('updated_at', 'desc')->where('hidden', 1)->whereHas(
                'departments',
                function ($q) {
                    return $q->whereIn('department_id', $this->getUserDepartments());
                }
            )->get()->toArray();
        }

        return array_filter(array_column($filesAndFolders, 'path'), function ($item) use ($disk) {
            return Cache::remember("{$item}-{$disk}-exists", now()->addMinutes(60), function () use ($disk, $item) {
                return Storage::disk($disk)->exists($item);
            });
        });
    }
}
