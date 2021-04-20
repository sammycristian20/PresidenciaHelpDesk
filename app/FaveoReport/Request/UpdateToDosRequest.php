<?php


namespace App\FaveoReport\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class UpdateToDosRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        return [
            "todos" => "array",
            "todos.*.name"=> "string|required",
            "todos.*.status"=> "string|required|in:pending,in-progress,completed"
        ];
    }
}