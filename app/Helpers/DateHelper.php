<?php

if (!function_exists('displayDateTime')) {

    /**
    * Convert UTC datetime to user timezone.
    */
    function displayDateTime($date,$timezone = 'Asia/Kolkata',$format = 'd-m-Y h:i A') {
        if (empty($date)) {
            return '-';
        }
        return \Carbon\Carbon::parse($date)
            ->setTimezone($timezone)
            ->format($format);
    }

    if (!function_exists('displayDate')) {
        /**
         * Display UTC date in Asia/Kolkata timezone.
         */
        function displayDate($date,string $timezone = 'Asia/Kolkata',string $format = 'd-m-Y') {
            if (empty($date)) {
                return '-';
            }

            return \Carbon\Carbon::parse($date)
                ->setTimezone($timezone)
                ->format($format);
        }
    }

    if (!function_exists('displayTime')) {
        /**
         * Display UTC time in Asia/Kolkata timezone.
         */
        function displayTime($date,string $timezone = 'Asia/Kolkata',string $format = 'h:i A') {

            if (empty($date)) {
                return '-';
            }

            return \Carbon\Carbon::parse($date)
                ->setTimezone($timezone)
                ->format($format);
        }
    }

}