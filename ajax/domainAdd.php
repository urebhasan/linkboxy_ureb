<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');

$user_id = $_SESSION['user_id'];

$domain = ((isset($_POST['domain'])) ? $_POST['domain'] : null);
$user_info = $user->user_info_by_id();

if($domain == null) $common->throwJsonError('Domain is empty.');

$user_websites = $user->user_websites();
if(count($user_websites) >= $package_data[$user_info['package']]['sites'])
	$common->throwJsonError('One Too Many Domains There! Please Upgrade The Package');

$stmt = $common->mysqli -> prepare("INSERT INTO users_websites(user_id,url,time) VALUES(?,?,?)");
$currentTime = time();
$stmt->bind_param("isi", $_SESSION['user_id'], $domain, $currentTime);
$stmt->execute();
$stmt->close();

$data = array(
	'user_id' => $user_id,
	'url' => $domain,
	'time' => $currentTime,
	'backlinks' => array()
);
$filename = "../storage/users/user".$user_id.".json";
if (file_exists($filename)){	//If user files exists
	$inp = file_get_contents($filename);
	$tempArray = json_decode($inp);
	$max = max(array_keys($tempArray)) + 1;
	$data['id'] = $max;
	$tempArray[$max] = $data;
	$jsonData = json_encode($tempArray);
	file_put_contents($filename, $jsonData);
}
else{
	$data['id'] = 0;
	$tempArray = array( 0 => $data);
	$jsonData = json_encode($tempArray);
	file_put_contents($filename, $jsonData);
}
//array_push($tempArray, $data);


$common->throwJsonSuccess('Website added! Now go to Manage Backlinks and add some backlinks to your site.');