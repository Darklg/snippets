<?php

/**
 * Extract all prices from a string
 * @param  string $str Entry string
 * @return array       Prices
 */
function dksnippets_extract_prices($str = '') {
    $prices = array();

    /* Clean string */
    $str = str_replace("\n", " ", strtolower($str));

    /* Remove useless numbers and fake results */
    $str = str_replace(array("24h", "7j", "00 %", ".00%", "*"), "", $str);
    $str = str_replace(", ", ",", $str);
    $str = str_replace("/ ", "/", $str);
    $str = str_replace(" ", "  ", $str);

    /* Extract all numbers surrounded by spaces */
    preg_match_all('/ \d+([\.,])\d+ /', $str, $matches_amount);
    if (isset($matches_amount[0][0])) {
        foreach ($matches_amount[0] as $nb) {
            $prices[] = floatval($nb);
        }
    }
    return $prices;
}


/**
 * Extract highest price from a string
 * @param  string $str Entry string
 * @return string      Highest price found (formatted : 11,11)
 */
function dksnippets_extract_highest_price($str = '') {
    $prices = dksnippets_extract_prices($str);

    /* Store highest amount */
    $highest_amount = 0;
    foreach ($prices as $price) {
        $tmp_nb = floatval($price);
        if ($tmp_nb > $highest_amount) {
            $highest_amount = $tmp_nb;
        }
    }
    return number_format(floatval(str_replace(',', '.', $highest_amount)), 2, ',', '');
}

/*
echo '<pre>';
var_dump(dksnippets_extract_highest_price(' 20,00 '));
var_dump(dksnippets_extract_highest_price(' 12,00  20,00 '));
var_dump(dksnippets_extract_highest_price(' 12.00  20,00 '));
var_dump(dksnippets_extract_highest_price('  20,00 12.00 '));
var_dump(dksnippets_extract_highest_price(' 012,00  11,00 '));
var_dump(dksnippets_extract_highest_price(' 012.00  11.00 '));
echo '</pre>';
*/

/**
 * Extract lowest price from a string
 * @param  string $str Entry string
 * @return string      Lowest price found (formatted : 11,11)
 */
function dksnippets_extract_lowest_price($str = '') {
    $prices = dksnippets_extract_prices($str);

    /* Store lowest amount */
    $lowest_amount = 999999999999;
    foreach ($prices as $price) {
        $tmp_nb = floatval($price);
        if ($tmp_nb < $lowest_amount) {
            $lowest_amount = $tmp_nb;
        }
    }
    return number_format(floatval(str_replace(',', '.', $lowest_amount)), 2, ',', '');
}

/*
echo '<pre>';
var_dump(dksnippets_extract_lowest_price(' 20,00 '));
var_dump(dksnippets_extract_lowest_price(' 12,00  20,00 '));
var_dump(dksnippets_extract_lowest_price(' 12.00  20,00 '));
var_dump(dksnippets_extract_lowest_price('  20,00 12.00 '));
var_dump(dksnippets_extract_lowest_price(' 012,00  11,00 '));
var_dump(dksnippets_extract_lowest_price(' 012.00  11.00 '));
echo '</pre>';
*/

