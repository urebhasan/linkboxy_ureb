<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');


$id = ((isset($_POST['id']) and ctype_digit($_POST['id'])) ? $_POST['id'] : null);

if($id == null) $common->throwJsonError('Error backlink.');

$user_backlinks = $user->user_backlinks();

$approved = false;
if(sizeof($user_backlinks) != 0){
	foreach($user_backlinks as $backlink){
		if($backlink['id'] == $id){
			$approved = true;
			continue;
		}
	}

	if($approved === false) $common->throwJsonError('Backlink error.');
}else{
	$common->throwJsonError('Create backlink first.');
}

$stmt = $common->mysqli -> prepare("DELETE users_backlinks,users_backlinks_link from users_backlinks
LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id
WHERE users_backlinks.id = ? and users_backlinks.user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();


$common->throwJsonSuccess('Backlink and all links are deleted!');