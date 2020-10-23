<?php
require('../init.php');

if ($user->user_authorized() === true) {
    $common->throwJsonError('Login failed. Already authorized.');
}

$login = (filter_var($_POST['login'], FILTER_VALIDATE_EMAIL)) ? $_POST['login'] : null;
$password = (!empty($_POST['password'])) ? $_POST['password'] : null;


if ($login == null || $password == null) {
    $common->throwJsonError('Login failed. Check email and password.');
}

if (!$user_info = $user->user_info_by_email($login)) {
    $common->throwJsonError('Login failed. Check email and password.');
}
if ($user_info['activate'] != NULL) {
    $common->throwJsonError('Please check your email and follow the accout activation link.');
}

if (password_verify($password, $user_info['password'])) {
    // If cost changed so need rehash
    if (password_needs_rehash($user_info['password'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $common->mysqli->prepare("UPDATE users SET password = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param("si", $newHash, $user_info['id']);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
    }

    $stmt = $common->mysqli->prepare("UPDATE users SET ip = INET_ATON(?) WHERE id=? LIMIT 1");
    $stmt->bind_param("si", $_SERVER['REMOTE_ADDR'], $user_info['id']);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();

    if ($user->authUserById($user_info['id'])) {
         $_SESSION['user_id'] = $user_info['id'];
		$_SESSION['reg_id']=$user_info['id'];
        $common->throwJsonSuccess('Logged in.');
    } else {
        $common->throwJsonError('Login failed due to server error. Try again later.');
    }
} else {
    $common->throwJsonError('Login failed.');
}