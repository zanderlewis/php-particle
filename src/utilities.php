<?php
function endswith($string, $ending)
{
    $length = strlen($ending);
    if ($length == 0) {
        return false;
    }
    return (substr($string, -$length) === $ending);
}

function startswith($string, $starting)
{
    $length = strlen($starting);
    if ($length == 0) {
        return false;
    }
    return (substr($string, 0, $length) === $starting);
}
