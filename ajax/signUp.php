<?php
require('../init.php');

if ($user->user_authorized() === true) {
	$common->throwJsonError('Login failed. Already authorized.');
}

$name = ((isset($_POST['name'])) ? substr(strip_tags($_POST['name']), 0, 29) : null);
$login = (filter_var($_POST['login'], FILTER_VALIDATE_EMAIL)) ? $_POST['login'] : null;
$password = ((isset($_POST['password']) and !empty($_POST['password'])) ? $_POST['password'] : null);
$package = ((isset($_POST['package']) and !empty($_POST['package'])) ? $_POST['package'] : null);

if ($package == null) {
	$common->throwJsonError('Please select package.');
}

if ($name == null) {
	$common->throwJsonError('Please check name you entered.');
}


if ($login == null) {
	$common->throwJsonError('Please check email you entered.');
}
if ($password == null) {
	$common->throwJsonError('Please check password you entered.');
}

if ($user->user_info_by_email($login)) {
	$common->throwJsonError('User with this email already exist. Please retrieve your password.');
}

if ($activationCode = $user->createUser($name, $login, $password, $package)) {
	$link = $common->domain ."/index.php?activation=$activationCode&package=$package";
	$text = "Congratulations! You successfully signed up to LinkBoxy.com<br/> To activate your account please follow this link: <a href='$link' target='_blank'>$link</a>";
	exec('php /home/u198566027/domains/app.linkboxy.com/public_html/cron/sendEmailDirect.php "' . $login . '" "Account activation on LinkBoxy.com" "' . $text . '"> /dev/null &');
	$common->sendMail($login, 'Backlink Checker. Account activation.', $text);
	$freeplans = ['monthly-freebie', 'yearly-freebie'];
	if (!in_array($package, $freeplans)) {
		$pack = $package_data[$package];

		$arr = ['success' => true, 'link' => 'true', 'item_name' => $pack['plan-name'], 'a3' => $pack['plan-price'] . '.00', 't3' => ($pack['plan-period'] == 'monthly') ? 'M' : 'Y', 'custom' => $package, 'text' => ''];
		echo json_encode($arr);
		die;
		//$common->throwJsonSuccess('Congratulations! Please check your email and follow the link to activate your account.');
	}
	$arr = ['success' => true, 'link' => '', 'text' => 'Congratulations! Please check your email and follow the link to activate your account.'];
	echo json_encode($arr);
	die;
	//$common->throwJsonSuccess('Congratulations! Please check your email and follow the link to activate your account.');
} else {
	$arr = ['error' => true, 'link' => '', 'text' => 'Sign up error.'];
	echo json_encode($arr);
	die;
	//$common->throwJsonError('Sign up error.');
}
