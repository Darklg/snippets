<?php

function dksnippets_extract_dates($str = '') {
    $dates = array();
    $matches = array();
    $str = str_replace(array("\n", "\r"), " ", $str);
    $str = str_replace(' ', '', $str);
    $str = str_replace('é', 'e', $str);
    $str = str_replace('û', 'u', $str);
    $str = strtolower($str);

    $months_list = array(
        array("janv.", "fevr.", "mars", "avril", "mai", "juin", "juil.", "aout", "sep.", "oct.", "nov.", "dec."),
        array("janvier", "fevrier", "mars", "avril", "mai", "juin", "juillet", "aout", "septembre", "octobre", "novembre", "decembre")
    );

    $month_after = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

    foreach ($months_list as $months_before) {
        foreach ($months_before as $i => $month) {
            $str = str_replace($month, '/' . $month_after[$i] . '/', $str);
        }
    }

    $dates_regex = array(
        /* Detect numeric dates (international format) : 20/10/2010 */
        '/[0-3]?[0-9][\/\.\-][0-1][0-9][\/\.\-]([1-2][0-9]{3})/isU',
        /* Detect dates with a YY year */
        '/[0-3]?[0-9][\/\.\-][0-1][0-9][\/\.\-](' . date('y') . '|' . (date('y') - 1) . ')/isU'
    );
    foreach ($dates_regex as $date_regex) {
        preg_match_all($date_regex, $str, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $match) {
                $match = str_replace(array('-', '.'), '/', $match);
                $match_items = array_map('intval', explode('/', $match));
                $time = mktime(12, 0, 0, $match_items[1], $match_items[0], $match_items[2]);
                if ($time < 0) {
                    continue;
                }
                $dates[$time] = date('d/m/Y', $time);
            }
        }
    }
    ksort($dates);
    return $dates;
}

/*
echo '<pre>';
// Year YY : Check current or previous year
var_dump(dksnippets_extract_dates(' 07/10/17 '));
var_dump(dksnippets_extract_dates(' 7/10/17 '));
// Year YYYY
var_dump(dksnippets_extract_dates(' 07-10-2012 '));
var_dump(dksnippets_extract_dates(' 07.03.2017 '));
var_dump(dksnippets_extract_dates(' 3 février 2017 '));
var_dump(dksnippets_extract_dates(' 19-10-2012 '));
var_dump(dksnippets_extract_dates(' 19/10/2012 '));
var_dump(dksnippets_extract_dates(' 19.10.2012 '));
var_dump(dksnippets_extract_dates(' 19 octobre 2012 '));
var_dump(dksnippets_extract_dates(' 19 oct. 2012 '));
echo '</pre>';
*/
