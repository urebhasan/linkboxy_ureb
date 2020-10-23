<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');


$oldP = ((isset($_POST['oldP'])) ? $_POST['oldP'] : null);
$newP = (isset($_POST['newP']) ? $_POST['newP'] : null);
$newPRepeat = (isset($_POST['newPRepeat']) ? $_POST['newPRepeat'] : null);


if($oldP == null) $common->throwJsonError('Enter old password');
if($newP == null) $common->throwJsonError('Enter new password');
if($newPRepeat == null) $common->throwJsonError('Repeat new password');
if($newP != $newPRepeat) $common->throwJsonError('New and repeat new password fields mismatch');

if(strlen($newP)<=5) $common->throwJsonError('New password must be at least 6 characters long');

$user_info = $user->user_info_by_id();
if(password_verify($oldP, $user_info['password'])){
	$stmt = $common->mysqli -> prepare("UPDATE users SET password = ? WHERE id = ? LIMIT 1");
	$hash = password_hash($newP, PASSWORD_DEFAULT);
	$stmt->bind_param("si", $hash, $user_info['id']);
	$stmt->execute();
	$stmt->close();
	$common->throwJsonSuccess('Password changed successfully! Please remember your new password.');
}else{
	$common->throwJsonError('Wrong current password.');
}


$common->throwJsonSuccess('Backlinks saved!');