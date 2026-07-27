<?php

if (! function_exists('convertToNumberOrdinal')) {
    /**
     * Converts a number to its ordinal representation in English.
     *
     * @param int $number The number to convert to its ordinal form
     *
     * @return string|int
     */
    function convertToNumberOrdinal($number)
    {
        if ($number === 0) {
            return 0;
        }

        if (! in_array($number % 100, [11, 12, 13], true)) {
            return match ($number % 10) {
                1 => $number . 'st',
                2 => $number . 'nd',
                3 => $number . 'rd',
                default => $number . 'th',
            };
        }

        return $number . 'th';
    }
}

if (! function_exists('convertToRoman')) {
    /**
     * Converts an integer to its Roman numeral representation.
     *
     * @param int $number The integer to convert to Roman numerals
     */
    function convertToRoman($number)
    {
        $romanNumber = '';
        $romanMap = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I',
        ];

        foreach ($romanMap as $value => $roman) {
            while ($number >= $value) {
                $romanNumber .= $roman;
                $number -= $value;
            }
        }

        return $romanNumber;
    }
}
