<?php
$local = array();

if (file_exists(__DIR__ . '/local.php')) {
    $local = include 'local.php';
}

$global = array();

return array_merge($local, $global);