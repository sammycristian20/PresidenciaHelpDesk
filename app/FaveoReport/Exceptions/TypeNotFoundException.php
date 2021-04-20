<?php


namespace App\FaveoReport\Exceptions;

use Exception;

class TypeNotFoundException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // no need to report this exception
    }
}