<?php

namespace common\helpers;

use common\models\OsagoRequest;
use DateTime;
use yii\web\BadRequestHttpException;

class DateHelper
{
    public static function date_format($date, $from_format, $to_format)
    {
        if ($formatted_date = date_create_from_format($from_format, $date))
            return $formatted_date->format($to_format);

        return $date;
    }

    public static function between_days($date1, $date2, $format)
    {
        $date1 = date_create_from_format($format, $date1)->getTimestamp();
        $date2 = date_create_from_format($format, $date2)->getTimestamp();
        $datediff = $date2 - $date1;

        return round($datediff / (60 * 60 * 24)) + 1;
    }

    public static function calc_age($format, $birthday, $when = null)
    {
        $birthday = date_create_from_format($format, $birthday);
        if ($when == null)
            $when = date_create(date("d-m-Y"));
        else
            $when = date_create_from_format($format, $when);

        return date_diff($when, $birthday)->format("%y");
    }

    public static function get_dates_of_quarter($quarter = 'current', $year = null, $format = null)
    {
        if ( !is_int($year) ) {
            $year = (new DateTime)->format('Y');
        }
        $current_quarter = ceil((new DateTime)->format('n') / 3);
        switch (  strtolower($quarter) ) {
            case 'this':
            case 'current':
                $quarter = ceil((new DateTime)->format('n') / 3);
                break;

            case 'previous':
                $year = (new DateTime)->format('Y');
                if ($current_quarter == 1) {
                    $quarter = 4;
                    $year--;
                } else {
                    $quarter =  $current_quarter - 1;
                }
                break;

            case 'first':
                $quarter = 1;
                break;

            case 'last':
                $quarter = 4;
                break;

            default:
                $quarter = (!is_int($quarter) || $quarter < 1 || $quarter > 4) ? $current_quarter : $quarter;
                break;
        }
        if ( $quarter === 'this' ) {
            $quarter = ceil((new DateTime)->format('n') / 3);
        }
        $start = new DateTime($year.'-'.(3*$quarter-2).'-1 00:00:00');
        $end = new DateTime($year.'-'.(3*$quarter).'-'.($quarter == 1 || $quarter == 4 ? 31 : 30) .' 23:59:59');

        return array(
            'start' => $format ? $start->format($format) : $start,
            'end' => $format ? $end->format($format) : $end,
        );
    }

    public static function birthday_from_pinfl($pinfl, $throw_error = true)
    {
        try {
            $pinfl = (string)$pinfl;
            switch ($pinfl[0])
            {
                case 3:
                case 4:
                    $centure = 19;
                    break;
                case 5:
                case 6:
                    $centure = 20;
                    break;
            }

            $birthday = mb_strcut($pinfl, 1, 2) . "." . mb_strcut($pinfl, 3, 2) . "." . $centure . mb_strcut($pinfl, 5, 2);
        }catch (\Exception $exception){
            if ($throw_error)
                throw new BadRequestHttpException(\Yii::t('app', 'incorrect pinfl'), 1);

            return null;
        }

        return $birthday;
    }
}