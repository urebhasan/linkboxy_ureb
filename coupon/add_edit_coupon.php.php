<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '756M');

require( '../init.php' );

$coupon_name = ((isset($_POST['coupon_name'])) ? $_POST['coupon_name'] : null);
$coupon_date = ((isset($_POST['coupon_date'])) ? $_POST['coupon_date'] : null);
$coupon_discount = ((isset($_POST['coupon_discount'])) ? $_POST['coupon_discount'] : null);

$coupon->createCoupon($coupon_name,$coupon_date,$coupon_discount);
