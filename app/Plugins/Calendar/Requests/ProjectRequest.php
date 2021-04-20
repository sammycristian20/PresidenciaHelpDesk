<?php
namespace App\Plugins\Calendar\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class ProjectRequest extends Request{

    use RequestJsonValidation;

	public function authorize(){
        return true;
    }

    public function rules(){

        $rules =  [
            'name' => 'required|string|max:50'
        ];

        if ($this->getMethod() == 'POST') {
            $rules['name'] = $rules['name']."|unique:projects";
        }

        return $rules;
    }

}

