<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');

$user_websites = $user->user_websites();
if(sizeof($user_websites) == 0){
	exit(json_encode(['success'=>true,
		'count'=>0
	]));	
}else{
	$ws = [];
	foreach($user_websites as $website){
		$ws[] = ['id' => $website['id'], 'domain' => $website['url'], 'backlinks' => ($website['backlinks_count']==null ? 0 : $website['backlinks_count']), 'checked' => ($website['success_backlinks']==null ? 0 : $website['success_backlinks']), 'failed' => ($website['failed_backlinks']==null ? 0 : $website['failed_backlinks']), 'unchecked' => ($website['unchecked_backlinks']==null ? 0 : $website['unchecked_backlinks']), 'nofollow' => ($website['nofollow_backlinks']==null ? 0 : $website['nofollow_backlinks'])];
	}
}

exit(json_encode(['success' => true,
	'count' => sizeof($ws),
	'websites' => $ws
]));