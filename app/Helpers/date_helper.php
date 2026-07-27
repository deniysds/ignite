<?php

use Carbon\Carbon;

if (! function_exists('dateFormat')) {
    /**
     * Formats a date string according to specified parameters.
     *
     * This function uses Carbon to parse a date and format it according to the locale
     * and display preferences provided.
     *
     * @param string|\DateTimeInterface $tanggal The date to be formatted
     * @param bool                      $time    Whether to include time in the formatted output
     * @param bool                      $showDay Whether to include the day name in the formatted output
     * @param string                    $locale  The locale to use for formatting
     */
    function dateFormat($tanggal, $time = false, $showDay = false, $locale = 'id_ID')
    {
        $carbon = Carbon::parse($tanggal)->locale($locale);

        if ($showDay && $time) {
            return $carbon->isoFormat('dddd, LL HH:mm:ss');
        }

        if ($showDay) {
            return $carbon->isoFormat('dddd, LL');
        }

        if ($time) {
            return $carbon->isoFormat('LL HH:mm:ss');
        }

        return $carbon->isoFormat('LL');
    }
}
