<?php


namespace App\Rules;

use App\Traits\FaveoDateParser;
use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;

class TimeDiffValidation implements Rule
{
    use FaveoDateParser;

    private $validationMessage = "Invalid Time diff Format";

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            return $this->isValidTimeDiff($value);
        } catch (InvalidArgumentException $e) {
            $this->validationMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->validationMessage;
    }
}
