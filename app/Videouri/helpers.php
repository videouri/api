<?php

/**
 * Recursive in_array function
 *
 * @param  array $needle
 * @param  array $haystack
 * @return boolean
 */
function in_array_r($needle, $haystack)
{
    if (!is_array($needle)) {
        return in_array_r(array($needle), $haystack);
    }
    foreach ($needle as $item) {
        if (in_array($item, $haystack)) {
            return true;
        }
    }
    return false;
}

/**
 * Try and get the acurrate IP
 * @return string IP
 */
function getUserIPAdress()
{
    //check ip from share internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    //to check ip is pass from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}
/**
 * Return user's country, based on his IP
 * @return string Country
 */
function getUserCountry($ip = null)
{
    if (empty($ip)) {
        $ip = getUserIPAdress();
        if ($ip !== '127.0.0.1') {
            return geoip_country_code_by_name($ip);
        }

        return 'UK';
    }

    return geoip_country_code_by_name($ip);
}