<?php
require( '../init.php' );

header('Content-Type: application/json');
$custom = $_POST['custom'];
$pack = $package_data[$custom];
$coupon_code = $_POST['coupon_code'];
$planprice = $_POST['planprice'];
$result['success'] = false;
$result['message'] = 'Issue in validating coupon code!';
$resultCoupon = $coupon->getCoupon($coupon_code);
if(!empty($pack['plan-price'])){
	if(empty($resultCoupon)){
		$result['success'] = false;
		$result['message'] = 'Invalid Coupon Code!';
	}else{
		$resultCoupon = $resultCoupon[0];
		$result['success'] = true;
		$discount_per = $resultCoupon['coupon_discount']/100;
		$coupon_amount = $planprice*$discount_per;
		$planprice = $planprice-$coupon_amount;
		$result['planprice'] = $planprice;
		$result['message'] = 'Applied!';
		$profileEmail = '$'.$planprice.'/';
		$profileEmail .= ($pack['plan-period'] == 'monthly') ? 'Month' : 'Year';
		$result['profileEmail'] = $profileEmail;
	}
}
echo json_encode($result);