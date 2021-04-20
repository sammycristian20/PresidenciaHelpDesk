<?php
namespace App\Bill\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;

class PaymentRequest extends Request
{
    use RequestJsonValidation;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'invoice'       => 'required|numeric',
            'method'        => 'required',
            'transactionId' => 'required',
            'amount'        => 'required|numeric'
        ];
    }
}