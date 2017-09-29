<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;

/**
 * 码验证
 *
 * Class VerifyPhone
 * @package App\Rules
 */
class VerifyPhone implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $phone = request()->get(last(explode('_',$attribute)));
        return Cache::get($phone) == $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '验证码错误';
    }
}
