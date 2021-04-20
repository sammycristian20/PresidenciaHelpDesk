<?php

namespace App\FileManager\Helpers;

use File;
use Storage;

class PasteHelper
{
    private $destinationPath;
    private $sourcePath;
    private $type;

    public function __construct($type, $sourcePath, $destinationPath)
    {
        $this->type = $type;
        $this->sourcePath = $sourcePath;
        $this->destinationPath = $destinationPath;
    }

    public function pasteFilesAndFolders()
    {
        ['files' => $files, 'directories' => $directories] = $this->getFilesAndFoldersInSourcePath();

        foreach ($directories as $directory) {
            ($this->type === 'copy')
                ? File::copyDirectory($directory, $this->destinationPath . "/" . basename($directory))
                : File::moveDirectory($directory, $this->destinationPath . "/" . basename($directory));
        }

        foreach ($files as $file) {
            ($this->type === 'copy')
                ? File::copy($file, $this->destinationPath . "/" . basename($file))
                : File::move($file, $this->destinationPath . "/" . basename($file));
        }
    }

    /**
     * Returns files and directories in the `source` path
     * @return array
     */
    private function getFilesAndFoldersInSourcePath(): array
    {
        $directories = File::directories($this->sourcePath);

        $files = File::files($this->sourcePath);

        return compact('files', 'directories');
    }
}
