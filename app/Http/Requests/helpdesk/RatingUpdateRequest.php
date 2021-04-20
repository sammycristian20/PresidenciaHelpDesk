<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

class RatingUpdateRequest extends Request
{
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

        // dd($this->segment(2));
        return [
            'name'               => 'unique:ratings,name,'.$this->segment(2),
            'display_order'      => 'required|integer|unique:ratings,display_order,'.$this->segment(2),
            'allow_modification' => 'required',
            'rating_scale'       => 'required',
            'restrict'           => 'required',
        ];
    }
}
