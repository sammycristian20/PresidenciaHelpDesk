<?php


namespace App\FaveoReport\Request;


use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class CreateToDoRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        return [
            "name" => "string|required"
        ];
    }
}