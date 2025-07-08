<?php

use Carbon\Carbon;

if (!function_exists('thai_date')) {
    /**     
     * @param string|Carbon $date
     * @return string
     */
    function thai_datetime($datetime)
    {
            $carbon = $datetime instanceof Carbon ? $datetime : Carbon::parse($datetime);
        $carbon->locale('th');
        $buddhistYear = $carbon->year + 543;

        return $carbon->format('d') . ' ' .
               $carbon->translatedFormat('M') . ' ' .
               $buddhistYear . ' เวลา ' .
               $carbon->format('H:i') . ' น.';
    }

     function thai_date($date)
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $carbon->locale('th');
        $buddhistYear = $carbon->year + 543;

        return $carbon->format('d') . ' ' . $carbon->translatedFormat('M') . ' ' . $buddhistYear;
    }
}
