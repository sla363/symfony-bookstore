<?php

namespace App\Service;

class DateManager
{
    public function createDateFromMultipleFormats($value, $hour = 0, $minute = 0, $second = 0, array $formats = null): ?\DateTime
    {
        if (!$formats) {
            $formats = [
                'Y-m-d H:i:s',
                'Ymd H:i:s',
                'Y/m/d H:i:s',
                'd/m/Y H:i:s',
                'd-m-Y H:i:s',
                'd.m.Y H:i:s',
                'd/m/yyyy h:mm',
                'd/m/yyyy h:mm:ss AM/PM',
                'm/d/Y H:i:s',
                'm-d-Y H:i:s',
                'm/d/yyyy h:mm',
                'm/d/yyyy h:mm:ss AM/PM',
                'yyyy-mm-dd h:mm',
                'yyyy-mm-dd h:mm:ss AM/PM',
                'd/m/Y',
                'd-m-Y',
                'd.m.Y',
                'd m Y',
                'm/d/Y',
                'm-d-Y',
                'm d Y',
                'Y-m-d',
                'd/m/yyyy',
                'm/d/yyyy',
                'yyyy-mm-dd'
            ];
        }

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date !== false) {
                $date->setTime($hour, $minute, $second);
                return $date;
            }
        }
        return null;
    }
}