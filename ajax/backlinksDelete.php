<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');


$id = ((isset($_POST['id']) and ctype_digit($_POST['id'])) ? $_POST['id'] : null);

if($id == null) $common->throwJsonError('Error backlink.');

$user_backlinks = $user->user_backlinks();

$approved = false;
if(sizeof($user_backlinks) == 0){
	$common->throwJsonError('Create backlink first.');
}

$stmt = $common->mysqli -> prepare("DELETE users_backlinks_link from users_backlinks_link
LEFT JOIN users_backlinks on users_backlinks.id = users_backlinks_link.backlink_id
WHERE users_backlinks_link.id = ? and users_backlinks.user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();


$common->throwJsonSuccess('Backlink deleted!');