<?php
namespace App\Plugins\Calendar\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class TaskCategoryRequest extends Request{

    use RequestJsonValidation;

    public function rules(){

        $rules =  [
            'name' => 'required|string|max:50',
            'project_id' => 'required',
        ];

        if ($this->getMethod() == 'POST') {
            $rules['name'] = $rules['name']."|unique:task_categories";
        }
        return $rules;
    }

}

