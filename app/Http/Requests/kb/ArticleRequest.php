<?php

namespace App\Http\Requests\kb;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class ArticleRequest extends Request
{

    use RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validationArray = [
            'name' => 'required|unique:kb_article',
            'slug' => 'required|unique:kb_article',
            'description' => 'required',
            'publish_time' => 'required',
            'author' => 'required',
            'is_comment_enabled' => 'sometimes|integer|in:0,1',
        ];

        if (Request::get('articleid')) {
            
            $validationArray['name'] = 'required|unique:kb_article,name,' . Request::get('articleid');

            $validationArray['slug'] = 'required|unique:kb_article,slug,' . Request::get('articleid') . ',id';

            $validationArray['category_id'] = 'required';
        }

        return $validationArray;
    }

}
