<?php

namespace App\Http\Requests\helpdesk\Organisation;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;


/**
 * OrganizationUpdate.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class OrganizationDepartmentRequest extends Request
{
	use RequestJsonValidation;

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
		   'org_deptname'   => 'max:30|unique:organization_dept,org_deptname,'.$this->org_dept_id,
		  
		];
	}
}