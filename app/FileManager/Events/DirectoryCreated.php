<?php

namespace App\FileManager\Events;

use Illuminate\Http\Request;

class DirectoryCreated
{
    /**
     * @var string
     */
    private $disk;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $page;

    /**
     * DirectoryCreated constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->disk = $request->input('disk');
        $this->path = $request->input('path');
        $this->name = $request->input('name');
        $this->page = $request->input('page');
    }

    /**
     * @return string
     */
    public function disk()
    {
        return $this->disk;
    }

    /**
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function page()
    {
        return $this->page;
    }
}
