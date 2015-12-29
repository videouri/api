<?php

function videouri_asset($path)
{
    if (env('APP_SECURE') === true) {
        $path = secure_asset($path);
    } else {
        $path = asset($path);
    }

    return $path;
}

function videouri_url($url)
{
    if (env('APP_SECURE') === true) {
        $url = secure_url($url);
    } else {
        $url = url($url);
    }

    return $url;
}

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
    } else {
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

        return 'GB';
    }

    return strtolower(geoip_country_code_by_name($ip));
}

/**
 * Convert ISO 8601 values like PT15M33S
 * to a total value of seconds.
 *
 * @param string $ISO8601
 */
function ISO8601ToSeconds($ISO8601)
{
    preg_match('/\d{1,2}[H]/', $ISO8601, $hours);
    preg_match('/\d{1,2}[M]/', $ISO8601, $minutes);
    preg_match('/\d{1,2}[S]/', $ISO8601, $seconds);

    $duration = [
        'hours'   => $hours ? $hours[0] : 0,
        'minutes' => $minutes ? $minutes[0] : 0,
        'seconds' => $seconds ? $seconds[0] : 0
    ];

    $hours = substr($duration['hours'], 0, -1);
    $minutes = substr($duration['minutes'], 0, -1);
    $seconds = substr($duration['seconds'], 0, -1);

    $toltalSeconds = ($hours * 60 * 60) + ($minutes * 60) + $seconds;

    return $toltalSeconds;
}

/**
 * Humanize numbers.
 * For example: 5000 would become 5K
 *
 * @param  int $number
 * @return return string
 */
function humanizeNumber($number)
{
    $abbrevs = array(12 => "T", 9 => "B", 6 => "M", 3 => "K", 0 => "");

    foreach ($abbrevs as $exponent => $abbrev) {
        if ($number >= pow(10, $exponent)) {
            $display_num = $number / pow(10, $exponent);
            $decimals = ($exponent >= 3 && round($display_num) < 100) ? 1 : 0;
            return number_format($display_num, $decimals) . $abbrev;
        }
    }
}

function humanizeSeconds($seconds)
{
    if ($seconds > 86400) {
        $seconds = $seconds % 86400;
    }

    return gmdate('H:i:s', $seconds);
}

/**
 * Generate url containing a new page query value
 *
 * @param  Int $page
 * @param  String $direction
 * @return String
 */
function urlToPage($page, $direction)
{
    switch ($direction) {
        case 'next':
            $page = $page + 1;
            break;

        case 'previous':
            $page = $page - 1;
            break;
    }

    $pageQuery = array('page' => $page);
    $currentQuery = Input::query();

    // Merge our new query parameters into the current query string
    $query = array_merge($currentQuery, $pageQuery);

    // dd(URL::route('results', $query));

    // Redirect to our route with the new query string
    return URL::route('search', $query);
}
