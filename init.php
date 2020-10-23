<?php
ini_set("display_errors", "On");
ini_set("error_reporting", E_ALL);

session_start();
require('config.php');

spl_autoload_register(function ($class) use ($config) {
    require($config['server_dir'] . 'classes/' . $class . '.class.php');
});

$common = new common($config);
$user = new user($config);
$coupon = new coupon($config);
$assignpackage = new assignpackage($config);
