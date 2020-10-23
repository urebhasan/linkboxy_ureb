<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require( '../init.php' );
if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');


$id = ((isset($_POST['id']) and ctype_digit($_POST['id'])) ? $_POST['id'] : null);

if($id == null) $common->throwJsonError('Error backlink.');

$user_websites = $user->user_websites();

$approved = false;
if(sizeof($user_websites) != 0){
	foreach($user_websites as $website){
		if($website['id'] == $id){
			$approved = true;
			continue;
		}
	}

	if($approved === false) $common->throwJsonError('Domain error.');
}else{
	$common->throwJsonError('Create domain first.');
}


$stmt = $common->mysqli -> prepare("DELETE users_websites, users_backlinks, users_backlinks_link from users_websites
LEFT JOIN users_backlinks on users_backlinks.domain_id = users_websites.id
LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id 
WHERE users_websites.id = ? and users_websites.user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();


$common->throwJsonSuccess('Domain and backlinks are deleted!');