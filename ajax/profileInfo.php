<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');

if(!$user_info = $user->user_info_by_id()) $common->throwJsonError('Server error #5812');

exit(json_encode(['success'=>true,
	'name'=>$user_info['name'],
	'email'=>$user_info['email'],
	'ip'=>long2ip($user_info['ip']),
	'backlinks'=>$user_info['user_backlinks'],
	'websites'=>$user_info['user_websites']
]));