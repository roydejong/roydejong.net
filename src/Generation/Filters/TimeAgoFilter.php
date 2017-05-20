<?php

namespace roydejong\dotnet\Generation\Filters;


class TimeAgoFilter extends \Twig_Filter
{
    public function __construct($name)
    {
        parent::__construct($name, [$this, 'filter'], []);
    }

    public static function filter($input): string
    {
        $time = 0;

        if ($input instanceof \DateTime) {
            $time = $input->getTimestamp();
        } else {
            $time = strtotime(strval($input));
        }

        $time = time() - $time;

        $units = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($units as $unit => $val) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);

            return ($val == 'second') ? 'a few seconds ago' :
                (($numberOfUnits > 1) ? $numberOfUnits : 'a')
                . ' ' . $val . (($numberOfUnits > 1) ? 's' : '') . ' ago';
        }

        return $input;
    }
}