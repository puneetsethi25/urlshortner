<?php

$routes = [];

function c($exp, $dump = false)
{
    echo '<pre>';
    ($dump ? var_dump($exp) : print_r($exp));
    echo '</pre>';
}
