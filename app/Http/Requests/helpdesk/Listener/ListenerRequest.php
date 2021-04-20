<?php

namespace App\Http\Requests\helpdesk\Listener;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListenerRequest extends FormRequest
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
        return [
            'listeners.name'   => 'required|unique:listeners,name,'.$this->segment(3),
            'listeners.status' => [
                Rule::in(['0', '1']),
            ],
            'listeners.rule_match' => [
                Rule::in(['any', 'all']),
            ],
            'events' => 'required',
            'events.*.event' =>'required',
            'events.*.condition' =>'required_unless:events.*.event,duedate,reply,note',
            'events.*.old' => [
                'required_unless:events.*.event,duedate,reply,note',
            ],
            'events.*.new' => [
                'required_unless:events.*.event,duedate,reply,note',
            ],
            'rules.*.condition'       => 'required_with:rules.*.key',
            'rules.*.value'           => 'required_with:rules.*.key',
            'actions'                 => 'required',
            'actions.*.value'         => 'required_with:actions.*.key',
            'actions.*.meta.receiver' => 'required_if:actions.*.key,mail',
            'actions.*.meta.subject'  => 'required_if:actions.*.key,mail',
            'actions.*.meta.content'  => 'required_if:actions.*.key,mail',
            'actions.*.key'           => function($attribute, $value, $fail) {
                $array = array_map(function($value){
                    return $value['key'];
                }, $this->request->all()['actions']);
                if (in_array('team', $array) && in_array('assigned_to', $array)) {
                    $attribute = trans('lang.listener-actions');
                    return $fail(trans('lang.select_either_agent_or_team', ['attr' => $attribute])); 
                }
            }
        ];
    }
}
