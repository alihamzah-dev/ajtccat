<?php

if (!function_exists('my_asset')) {
    function my_asset($path, $secure = null)
    {
        return asset('/'.trim($path, '/').'?v='.filesize(public_path('/'.trim($path, '/'))), $secure);
    }
}
function set_min_scale($score)
{
    $max = (round($score / 10) - 1) * 10;

    return $max;
}
function set_max_scale($score)
{
    $max = (round($score / 10) + 2) * 10;

    return $max;
}
function get_color_coded($value)
{
    if ($value >= 131) {
        return '#fcc5c0';
    } elseif ($value >= 121) {
        return '#6cc6b1';
    } elseif ($value >= 111) {
        return '#66be68';
    } elseif ($value >= 90) {
        return '#399752';
    } elseif ($value >= 80) {
        return '#cc4c02';
    } elseif ($value >= 70) {
        return '#993404';
    } else {
        return '#800026';
    }
}
