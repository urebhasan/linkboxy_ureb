<?php
require( '../init.php' );

if($user->user_authorized() === false)  $common->throwJsonError('Not logged in.');

if(isset($_COOKIE['cookiescookies']) and !empty($_COOKIE['cookiescookies'])) {
if(!$explode=explode(':', $_COOKIE['cookiescookies'])){return 0;}
list($hash, $token) = $explode;
	
$stmt = $common->mysqli -> prepare("SELECT * FROM users_tokens WHERE hash=? ORDER by id DESC LIMIT 1");
$stmt->bind_param("i", $hash);
$stmt->execute();
$result = $stmt->get_result();
$stmt -> close();
$data = $result->fetch_all(MYSQLI_ASSOC);
	
if($data['0']['id']>0){
	$stmt = $common->mysqli -> prepare("DELETE from users_tokens WHERE id=?");
	$stmt->bind_param("i", $data['0']['id']);
	$stmt->execute();
	$stmt -> fetch();
	$stmt -> close();
}
setcookie("cookiescookies", '', time()-3333333, '/', $common->domain, false); // удаляем cookie авторизации
}

if(session_destroy()){
	$common->throwJsonSuccess('Signed out');
}