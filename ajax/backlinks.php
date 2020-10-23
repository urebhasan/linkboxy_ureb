<?php
require( '../init.php' );

if($user->user_authorized() !== true)  $common->throwJsonError('Login failed.');

$user_backlinks = $user->user_backlinks();
$user_websites = $user->user_websites();

$websites = [];
if(sizeof($user_websites) != 0){
	foreach($user_websites as $website){
		$websites[] = ['id' => $website['id'], 'url' => $website['url'], 'time' => date('d.m.Y H:i:s', $website['time'])];
	}
}

if(sizeof($user_backlinks) == 0){
	exit(json_encode(['success'=>true,
		'count'=>0,
		'sites'=>$websites
	]));	
}else{
	$backlinks = [];
	foreach($user_backlinks as $backlink){
		
		$backlinks[] = ['id' => $backlink['id'], 'backlinks_link_id' => $backlink['backlinks_link_id'], 'url_id' => $backlink['domain_id'], 'url' => $backlink['backlink_url'], 'backlink_url' => $backlink['backlink_link_url'], 'backlink_status' => $backlink['backlink_status'], 'status' => $backlink['status'], 'nofollow' => $backlink['nofollow'], 'anchor' => $backlink['anchor_text'], 'domain' => $backlink['domain'], 'updated' => ($backlink['time_checked']!=null ? date('d.m.Y H:i',$backlink['time_checked']) : null)];

	}
}

exit(json_encode(['success' => true,
	'count' => sizeof($backlinks),
	'backlinks' => $backlinks,
	'sites' => $websites
]));