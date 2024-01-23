<?php

namespace App\Helpers;

class PhoneNumberHelper
{
    public static function generateRandomPhoneNumber(): string
    {
        $phoneNumberLen = rand(3, 20);

        $phoneNumberStr = '';

        for ($i = 0; $i < $phoneNumberLen; $i++) {
            $phoneNumberStr .= rand(0, 9);
        }

        return $phoneNumberStr;
    }
}
