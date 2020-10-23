<?php
require( '../init.php' );

if($user->user_authorized() === false)  $common->throwJsonError('Not logged in.');

header('Content-Type: application/json');
			        // 'event': event, 
			        // 'venue': venue,
			        // 'weekdays': weekdays,
			        // 'trapno': trapno,
			        // 'orderside': orderside,
			        // 'pricetrigger': pricetrigger,
			        // 'stakevalue': stakevalue,
			        // 'is_active': is_active

$event = (isset($_POST['event']) ? $_POST['event'] : null);
$venue = (isset($_POST['venue']) ? $_POST['venue'] : null);
$weekdays = ((isset($_POST['weekdays']) and is_array($_POST['weekdays'])) ? $_POST['weekdays'] : null);
$trapno = ((isset($_POST['trapno']) and is_array($_POST['trapno'])) ? $_POST['trapno'] : null);
$orderside = (isset($_POST['orderside']) ? $_POST['orderside'] : null);
$pricetrigger = (isset($_POST['pricetrigger']) ? $_POST['pricetrigger'] : null);
$stakevalue = (isset($_POST['stakevalue']) ? $_POST['stakevalue'] : null);
$is_active = (isset($_POST['is_active']) ? $_POST['is_active'] : null);
$isNewTask = ((isset($_POST['is_new_task']) and ctype_digit($_POST['is_new_task'])) ? $_POST['is_new_task'] : null);

if($event == NULL or $venue == NULL or $weekdays == NULL or $trapno == NULL or $orderside == NULL or $pricetrigger == NULL or $stakevalue == NULL){
	$common->log('Strange POST data in ajax/addTask.php, _POST: '.print_r($_POST,true));
	echo json_encode(array('status'=>'fail'));
	exit();
}

$check_weekdays = [];
foreach ($weekdays as $day) {
	if(!is_numeric($day)) continue;
	$check_weekdays[] = $day;
}

$weekdays = implode(',',$check_weekdays);

$check_trapno = [];
foreach ($trapno as $tn) {
	if(!is_numeric($tn)) continue;
	$check_trapno[] = $tn;
}

$trapno = implode(',',$check_trapno);


if($isNewTask == null){
	$x = $common->insertTask($event, $venue, $weekdays, $trapno, $orderside, $pricetrigger, $stakevalue, $is_active);
}else{
	$x = $common->updateTask($isNewTask, $event, $venue, $weekdays, $trapno, $orderside, $pricetrigger, $stakevalue, $is_active);
}

if($x){
	$status = 'success';
}else{
	$status = 'fail';
}

echo json_encode(array('status'=>$status));