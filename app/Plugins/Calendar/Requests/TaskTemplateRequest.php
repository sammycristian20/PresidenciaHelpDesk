<?php

namespace App\Plugins\Calendar\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class TaskTemplateRequest extends Request
{
    use RequestJsonValidation;

    public function rules()
    {
        $rules = [
            'category_id' => 'nullable',
            'name' => 'required|max:50',
            'description' => 'required|max:1000',
            'task_templates' => 'required|array',
            'task_templates.*.assignees' => 'nullable',
            'task_templates.*.taskEnd' => 'required|integer|min:1|max:1000',
            'task_templates.*.taskEndUnit' => 'required',
            'task_templates.*.order' => 'required',
            'task_templates.*.taskName' => 'required|max:50',
            'task_templates.*.assignTaskToTicketAssignee' => 'required'
        ];

        if($this->getMethod() =='POST') {
            $rules['name'] = $rules['name'].'|unique:task_templates';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'project_id.required' => 'This field is required',
            'name.required' => 'This field is required',
            'name.max' => 'Task Template Name should be less than 50 characters.',
            'name.unique' => 'Task Template Name is already taken.',
            'description.required' => 'This field is required',
            'description.max' => 'Description should be less than 1000 characters',
            'task_templates.*.assignees.required' => 'nullable',
            'task_templates.*.taskEnd.required' => 'Required',
            'task_templates.*.taskEndUnit.required' => 'Required',
            'task_templates.*.order.required' => 'Required',
            'task_templates.*.taskName.required' => 'Required',
            'task_templates.*.taskName.max' => 'Must be less than 50 characters',
            'task_templates.*.taskEnd.integer' => 'Must be an integer number',
            'task_templates.*.taskEnd.min' => 'Must be greater than 0',
            'task_templates.*.taskEnd.max' => 'Must be lesser than 1000',
            'task_templates.*.assignTaskToTicketAssignee.required' => 'Required',
        ];
    }

}