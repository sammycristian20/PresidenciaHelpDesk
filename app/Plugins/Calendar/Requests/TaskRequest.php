<?php

namespace App\Plugins\Calendar\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

/**
 * AgentRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class TaskRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        $rules = [
            "task_name"         => 'required|max:50',
            "task_description"  => 'nullable|max:1000',
            "task_start_date"   => 'required|date',
            "task_end_date"     => 'required|date|after:task_start_date',
            "task_category_id"  => 'nullable',
            "due_alert"         => 'nullable',
            "is_private"        => 'required',
            'status'            => 'required'
        ];

        if (request('is_private') == '1') {
            $rules['associated_ticket'] = 'size:0';
            $rules['assignee'] = 'size:0';
        } else {
            $rules['associated_ticket'] = 'required|integer';
            $rules['assignee'] = 'required|array';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'associated_ticket.size'  => trans('Calendar::lang.task_ticket_invalid_message'),
            'assignee.size'           => trans('Calendar::lang.task_assignee_invalid_message')
        ];
    }
}
