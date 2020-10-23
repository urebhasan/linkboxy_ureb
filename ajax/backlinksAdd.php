<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');


$domain = ((isset($_POST['domain']) and ctype_digit($_POST['domain'])) ? $_POST['domain'] : null);
$backlinks = (isset($_POST['backlinks']) ? $_POST['backlinks'] : null);

if($domain == null) $common->throwJsonError('Create domain first.');
if($backlinks == null) $common->throwJsonError('Enter backlinks.');

$user_backlinks = $user->user_backlinks();

$user_websites = $user->user_websites();
$user_info = $user->user_info_by_id();

if(count($user_backlinks) >= $package_data[$user_info['package']]['monitored-links'])
	$common->throwJsonError('Backlinks limit exceeding! Please Update package');

$approved = false;
if(sizeof($user_websites) != 0){
	foreach($user_websites as $website){
		if($website['id'] == $domain){
			$approved = true;
			continue;
		}
	}

	if($approved === false) $common->throwJsonError('Domain error.');
}else{
	$common->throwJsonError('Create domain first.');
}

$separator = "\r\n";
$line = strtok($backlinks, $separator);

$validated = [];
while ($line !== false){
    $validated[] = filter_var($line, FILTER_SANITIZE_URL);
    $line = strtok($separator);
}

$user_id = $_SESSION['user_id'];
foreach($validated as $validDomain){
	// $stmt = $common->mysqli -> prepare("INSERT INTO users_backlinks(user_id,url,domain_id,time_added,status) VALUES(?,?,?,?,?)");
	// $currentTime = time();
	// $status = 0;
	// $stmt->bind_param("isiii", $_SESSION['user_id'], $validDomain, $domain, $currentTime, $status);
	// $stmt->execute();
	// $stmt->close();

	// TEST PURPOSE
	// $stmt2 = $common->mysqli->prepare("SELECT * FROM users_websites where id = ?");
	// $stmt2->bind_param("i", $domain);
	// if ($stmt2->execute()) {
	// 	$result = $stmt2->get_result();
	// 	$stmt2->close();
	// 	// $rslt = $result->fetch_assoc(MYSQLI_ASSOC);

	// 	// $filename = "../storage/users/u_web_rslt".$user_id.".json";
	// 	// if (!file_exists($filename)){
	// 	// 	$fp = fopen($filename, 'w');
	// 	// 	fwrite($fp, json_encode($rslt));
	// 	// 	fclose($fp);
	// 	// }
	// }
	// END TEST PURPOSE
	

	$data = array(
		'user_id' => $user_id,
		'url' => $validDomain,
		'domain_id' => $domain,
		'time_added' => $currentTime,
		'status' => $status
	);
	$filename = "../storage/users/bl".$user_id.".json";
	$tempArray = array();
	if (file_exists($filename)){
		$inp = file_get_contents($filename);
		$tempArray = json_decode($inp);
	}
	array_push($tempArray, $data);
	$jsonData = json_encode($tempArray);
	file_put_contents($filename, $jsonData);

	// Get user file and edit
	$filename = "../storage/users/user".$user_id.".json";
	$user_file = file_get_contents($filename);
	$user_data = json_decode($user_file);
	//array_push($user_data[1]['backlinks'], $data);
	array_push($user_data, $data);
	$jsonData = json_encode($user_data);
	file_put_contents($filename, $jsonData);
}


$common->throwJsonSuccess('Backlinks Added - It may take a while to show depending on the amount you have added.');