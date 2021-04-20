<?php
namespace App\Plugins\Twitter\Requests;

use App\Http\Requests\Request;
use App\Traits\RequestJsonValidation;
use Illuminate\Support\Str;

class TwitterAppRequest extends Request
{
    use RequestJsonValidation;


    /**
     * We obtain hashtag values from user without the '#' symbol to avoid confusions
     * But when the information is fetched for tweets, '#' symbol is needed
     */
    public function prepareForValidation()
    {
        $hashTags = $this->get('hashtag_text');

        $hashTagWithPrecedingHashSymbol = array_map(function ($hashtag) {
            if (! Str::startsWith($hashtag, '#')) {
                return "#{$hashtag}";
            }
            return $hashtag;
        }, $hashTags);

        $this->merge(['hashtag_text' => $hashTagWithPrecedingHashSymbol]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'consumer_api_secret' => 'required|string',
            'consumer_api_key'    => 'required|string',
            'access_token'        => 'required|string',
            'access_token_secret' => 'required|string',
        ];

        if ($this->getMethod() == "POST") {
            $rules = array_map(function ($item) {
                return $item."|unique:twitter_app";
            }, $rules);
        }

        $rules['hashtag_text'] = 'nullable|array';
        return $rules;
    }
}
