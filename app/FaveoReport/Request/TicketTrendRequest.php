<?php


namespace App\FaveoReport\Request;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Auth;

class TicketTrendRequest extends Request
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
            'view_by'=>"required|in:day,week,month,year",
        ];
        return $rules;
    }
}