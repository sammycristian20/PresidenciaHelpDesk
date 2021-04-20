<?php

namespace App\FaveoReport\Exceptions;

use Exception;

class NullValueException extends Exception
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
