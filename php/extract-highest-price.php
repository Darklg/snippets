<?php

/**
 * Extract highest price from a string
 * @param  string $str String
 * @return string      Highest price found (formatted : 11,11)
 */
function dksnippets_extract_highest_price($str = '') {
    /* Clean string */
    $str = str_replace("\n", " ", strtolower($str));

    /* Remove useless numbers and fake results */
    $str = str_replace(array("24h", "7j", "00 %", ".00%", "*"), "", $str);
    $str = str_replace(", ", ",", $str);
    $str = str_replace("/ ", "/", $str);
    $str = str_replace(" ", "  ", $str);

    /* Extract all numbers surrounded by spaces */
    preg_match_all('/ \d+([\.,])\d+ /', $str, $matches_amount);
    $highest_amount = 0;
    if (isset($matches_amount[0][0])) {
        /* Store highest amount */
        foreach ($matches_amount[0] as $nb) {
            $tmp_nb = floatval($nb);
            if ($tmp_nb > $highest_amount) {
                $highest_amount = $nb;
            }
        }
    }
    return number_format(floatval(str_replace(',', '.', $highest_amount)), 2, ',', '');
}


/*
echo '<pre>';
var_dump(dksnippets_extract_highest_price(' 20,00 '));
var_dump(dksnippets_extract_highest_price(' 12,00  20,00 '));
var_dump(dksnippets_extract_highest_price(' 12.00  20,00 '));
var_dump(dksnippets_extract_highest_price(' 012,00  11,00 '));
var_dump(dksnippets_extract_highest_price(' 012.00  11.00 '));
echo '</pre>';
*/
