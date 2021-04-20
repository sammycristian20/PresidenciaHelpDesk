<?php

namespace App\FaveoReport\Exceptions;

use Exception;

class VariableNotFoundException extends Exception
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
