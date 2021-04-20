<?php

namespace App\FaveoReport\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Auth;
use Illuminate\Validation\Rule;

/**
 * validates the mailSettings request
 */
class CustomColumnRequest extends Request
{
	use RequestJsonValidation;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      if(!Auth::check()){
        return false;
      }

      if(Auth::user()->role == 'user'){
        return false;
      }

      return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => Rule::unique("report_columns", "label")->where(function($query){
                return $query->where("sub_report_id", $this->subReportId);
            })->ignore($this->id),

            'equation'=>"required",
        ];
        return $rules;
    }
}
