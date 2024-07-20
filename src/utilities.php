<?php
function endswith($string, $test)
{
    $length = strlen($test);
    if ($length == 0) {
        return false;
    }
    return (substr($string, -$length) === $test);
}