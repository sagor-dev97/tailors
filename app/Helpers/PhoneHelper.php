<?php

namespace App\Helpers;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class PhoneHelper
{
    /**
     * Parse and normalize phone number
     *
     * @param string $phone   Input phone number
     * @param string|null $country  ISO country code (optional)
     * @return array|null  Returns array with E164, country code, national number, ISO
     */
    public static function parsePhone($phone, $country = null)
    {
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($phone, $country);

            if (!$phoneUtil->isValidNumber($number)) {
                return null;
            }

            return [
                'phone_e164'      => $phoneUtil->format($number, PhoneNumberFormat::E164),
                'country_code'    => '+' . $number->getCountryCode(),
                'national_number' => $number->getNationalNumber(),
                'country_iso'     => $phoneUtil->getRegionCodeForNumber($number),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
