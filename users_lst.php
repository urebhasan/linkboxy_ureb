<?php
require( 'init.php' );

 if ($user->user_authorized() !== true)  {
     die('Error');
 }

 $admin_emails = array(
     'spatel1981@hotmail.com',
     'colinlma@gmail.com'
//     'unknownmark@yandex.ru',
 );
 $user_data = $user->user_info_by_id();
 if ( !in_array( $user_data['email'], $admin_emails ) ) {
     die('Error');
 }

$utd = (isset($_GET['userid']) ? $_GET['userid'] : null);
$action = (isset($_GET['action']) ? $_GET['action'] : null);

if ($utd != null && $action == 'delete') {
    $stmt = $common->mysqli->prepare("DELETE users,users_websites,users_backlinks,users_backlinks_link FROM users LEFT JOIN users_websites on users_websites.user_id = users.id LEFT JOIN users_backlinks on users_backlinks.user_id = users.id LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id=users_backlinks.id WHERE users.id=?");
    $currentTime = time();
    $stmt->bind_param("i", $utd);
    if (!$stmt->execute()) return false;
    $result = $stmt->get_result();
    $stmt->close();
    header('Location: ' . basename($_SERVER['PHP_SELF']));
}
if ($utd != null && $action == 'login') {
    $user->authUserById($utd);
    header("Location: main.php");
    exit;
}

$stmt = $common->mysqli->prepare("SELECT id,email from users");
if (!$stmt->execute()) return false;
$result = $stmt->get_result();
$stmt->close();
$users = $result->fetch_all(MYSQLI_ASSOC);

foreach ($users as $k => $v) {
    echo $v['email'] . " - <a href='users_lst.php?userid=" . $v['id'] . "&action=login'>Login as user</a> <a href='users_lst.php?userid=" . $v['id'] . "&action=delete'>DELETE USER</a><br/>";
}