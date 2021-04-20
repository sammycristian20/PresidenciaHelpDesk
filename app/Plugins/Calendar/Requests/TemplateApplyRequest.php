<?php

namespace App\Plugins\Calendar\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class TemplateApplyRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        return [
            'template' => 'required',
            'ticketId' => 'required',
        ];
    }
}