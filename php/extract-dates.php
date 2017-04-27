<?php

function dksnippets_extract_dates($str = '') {
    $dates = array();
    $matches = array();
    $months_list = array(
        array("janv.", "fevr.", "mars", "avril", "mai", "juin", "juil.", "aout", "sep.", "oct.", "nov.", "dec."),
        array("janvier", "fevrier", "mars", "avril", "mai", "juin", "juillet", "aout", "septembre", "octobre", "novembre", "decembre"),
        array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'),
        array('jan', 'feb', 'mar', 'apr', 'may', 'june', 'july', 'aug', 'sept', 'oct', 'nov', 'dec')
    );
    $month_after = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

    /* Clean STR */
    $str = str_replace(array("\n", "\r"), " ", $str);
    $str = strtolower($str);
    $orig_str = $str;
    $str = str_replace(' ', '', $str);
    $str = str_replace('é', 'e', $str);
    $str = str_replace('û', 'u', $str);

    /* Detect probable year */
    $probable_year = date('Y');
    $years = array($probable_year => 1);
    preg_match_all('/(?:(?:19|20)[0-9]{2})/isU', $orig_str, $years_tmp);
    /* Extract years */
    if (!empty($years_tmp[0])) {
        foreach ($years_tmp[0] as $_year) {
            if (isset($years[$_year])) {
                $years[$_year]++;
            } else {
                $years[$_year] = 1;
            }
        }
    }
    /* Sort years by number of occurences */
    if (!empty($years)) {
        asort($years);
        end($years);
        $probable_year = key($years);
    }
    $probable_year_min = substr($probable_year, -2, 2);

    /* Replace month by number */
    foreach ($months_list as $months_before) {
        foreach ($months_before as $i => $month) {
            $str = str_replace($month, '/' . $month_after[$i] . '/', $str);
        }
    }

    $dates_regex = array(
        /* Detect numeric dates (international format) : 20/10/2010 */
        '/[0-3]?[0-9][\/\.\-][0-1][0-9][\/\.\-]([1-2][0-9]{3})/isU',
        /* Detect dates with a YY year */
        '/[0-3]?[0-9][\/\.\-][0-1][0-9][\/\.\-](' . $probable_year_min . '|' . ($probable_year_min - 1) . ')/isU'
    );
    foreach ($dates_regex as $date_regex) {
        preg_match_all($date_regex, $str, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $match) {
                $match = str_replace(array('-', '.'), '/', $match);
                $match_items = array_map('intval', explode('/', $match));
                $time = mktime(12, 0, 0, $match_items[1], $match_items[0], $match_items[2]);
                if ($time > 0) {
                    $dates[$time] = date('d/m/Y', $time);
                }
            }
        }
    }

    /* Search dates in textual format */
    foreach ($months_list as $month_list) {
        $month_list_regex = str_replace('.', '\.', implode('|', $month_list));
        /* FR Format : Day Month */
        $date_regex = '/([0-3]?[0-9]) (' . $month_list_regex . ')/is';
        preg_match_all($date_regex, $orig_str, $matches);
        if (!empty($matches[0])) {
            $found_month = array_search($matches[2][0], $month_list, false) + 1;
            $time = mktime(12, 0, 0, $found_month, $matches[1][0], $probable_year);
            if ($time > 0) {
                $dates[$time] = date('d/m/Y', $time);
            }
        }
        /* EN Format : Month Day */
        $date_regex_inv = '/(' . $month_list_regex . ') ([0-3]?[0-9][^0-9])/is';
        preg_match_all($date_regex_inv, $orig_str, $matches);
        if (!empty($matches[0])) {
            $found_month = array_search($matches[1][0], $month_list, false) + 1;
            $time = mktime(12, 0, 0, $found_month, $matches[2][0], $probable_year);
            if ($time > 0) {
                $dates[$time] = date('d/m/Y', $time);
            }
        }
    }

    ksort($dates);
    return $dates;
}

/* TESTS
-------------------------- */

/*
$tests = array(
    // Year YY : Check probable or previous year
    array(' 07/10/17 ', '07/10/2017'),
    array(' 7/10/17 ', '07/10/2017'),
    // Year YYYY
    array(' 07-10-2012 ', '07/10/2012'),
    array(' 07.03.2017 ', '07/03/2017'),
    array(' 3 février 2017 ', '03/02/2017'),
    array(' Feb 3, 2017 ', '03/02/2017'),
    array(' March 20, 2017 ', '20/03/2017'),
    array(' 19-10-2012 ', '19/10/2012'),
    array(' 19/10/2012 ', '19/10/2012'),
    array(' 19.10.2012 ', '19/10/2012'),
    array(' 19 octobre 2012 ', '19/10/2012'),
    array(' 19 oct. 2012 ', '19/10/2012'),
    // Errors
    array(' March 202 ', false),
    array(' Mars202 ', false),
);

echo '<table border="1" cellpadding="3">';
echo '<tr><th>Entry</th><th>Should be</th><th>1st Result</th><th>Status</th></tr>';
foreach ($tests as $test) {
    $values = dksnippets_extract_dates($test[0]);
    $value = current($values);
    echo '<tr>';
    echo '<td>' . $test[0] . '</td>';
    echo '<td>' . $test[1] . '</td>';
    echo '<td>' . $value . '</td>';
    echo '<td>' . ($value == $test[1] ? '<span style="color:green">valid</span>' : '<span style="color:red"><strong>invalid</strong></span>') . '</td>';
    echo '</tr>';
}
echo '</table>';

*/
