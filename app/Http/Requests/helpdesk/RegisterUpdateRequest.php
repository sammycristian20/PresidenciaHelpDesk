<?php

namespace App\Http\Requests\helpdesk;

use App\Http\Requests\Request;

/**
 * RegisterRequest.
 *
 * @author  Ladybird <info@ladybirdweb.com>
 */
class RegisterUpdateRequest extends Request
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
       $panel = 'agent';
       $rules = $this->check($panel);
       return $rules;
    }
    
    public function check($panel)
    {
        $required = \App\Model\Custom\Required::
                where('form','user')
                ->select("$panel as panel",'field','option')
                ->where(function($query)use($panel){
                    return $query->whereNotNull($panel)
                            ->where($panel,'!=','')
                            ;
                })
                ->get()
                ->transform(function($value){
                    $option = $value->option;
                    if($option){
                        $option = ",".$value->option;
                    }
                    $unique_validation = "";
                    if($value->field=='email'){
                        // $unique_validation = "email|requer|";
                    }
                    if($value->field=='mobile'){
                        // $unique_validation = "unique:users|";
                    }
                    $request[$value->field]=$unique_validation.$value->panel.$option;
                    return $request;
                })
                ->collapse()
                ->toArray();
                ;
                //dd($required);
        return $required;
    }
}