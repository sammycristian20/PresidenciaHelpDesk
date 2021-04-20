<?php

namespace App\Http\Requests\kb;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class ArticleTemplateRequest extends Request
{

    use RequestJsonValidation;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $validationArray = [
            'name' => 'required|max:25|unique:kb_article_template',
            'description' => 'required',
        ];
        if (Request::get('id')) {
            $validationArray['id'] = 'sometimes|exists:kb_article_template,id';

            $validationArray['name'] = 'required|max:25|unique:kb_article_template,name,' . Request::get('id');
        }
        return $validationArray;
    }

}
