<?php

namespace App\Http\Requests\helpdesk\Common;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * validates the mailSettings request
 */
class DataTableRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            'search_query'=>"string",
            'page'=>"numeric",
            'sort_field'=>"string",
            'sort_order'=>"in:asc,desc",
            'limit'=>'numeric',
        ];
        return $rules;
    }
}
