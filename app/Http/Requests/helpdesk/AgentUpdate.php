<?php
namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * AgentUpdate.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class AgentUpdate extends Request
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
    public function rules() {
        $rule = [
            'email'             => getEmailValidation().','.$this->segment(2),
            'user_name'         => [
                'required', 'min:3',
                'regex:/^(?:[A-Z\d][A-Z\d._-]{2,30}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,5})$/i',
                'unique:users,user_name,' . $this->segment(2)
            ],
            'first_name' => 'required|max:30|alpha',
            'mobile' => getMobileValidation('mobile') . ',' . $this->segment(2),
            'ext' => 'max:5',
            'phone_number' => 'max:15',
        ];
        if(count($this->getRuleAccourdingToRole()) > 0) {
            $rule = array_merge($rule, $this->getRuleAccourdingToRole());
        }
        return $rule;
    }

    /**
     * @param null
     * @category function to add validation rule for agent/admin account
     * @return array
     */
    public function getRuleAccourdingToRole()
    {
        if ($this->input('role')!='user') {
            return [
                'role'                => 'required',
                'primary_department'  => 'required',
                'agent_time_zone'     => 'required',
            ];
        }
        return [];
    }
}
