<?php
require( 'init.php' );

if ($user->user_authorized() !== true)  {
    die('You must be logged in');
}

$user_backlinks = $user->user_backlinks();
//$user_websites = $user->user_websites();
//
//$websites = [];
//if(sizeof($user_websites) != 0){
//    foreach($user_websites as $website){
//        $websites[] = ['id' => $website['id'], 'url' => $website['url'], 'time' => date('d.m.Y H:i:s', $website['time'])];
//    }
//}

function user_backlinks(){
    $stmt = $this->mysqli->prepare("SELECT users_backlinks.id, users_backlinks.status as backlink_status, users_backlinks.domain_id, users_backlinks.url as backlink_url,users_backlinks_link.id as backlinks_link_id, users_backlinks_link.url as backlink_link_url, users_backlinks_link.status, users_backlinks_link.nofollow, users_backlinks_link.anchor_text, users_backlinks_link.time_checked, users_websites.url as domain from users_backlinks
LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id
LEFT JOIN users_websites on users_websites.id = users_backlinks.domain_id
WHERE users_backlinks.user_id = ? ORDER by id DESC, users_backlinks.time_added DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    if(!$stmt->execute()) return false;
    $result = $stmt->get_result();
    $stmt->close();
    $rslt = $result->fetch_all(MYSQLI_ASSOC);

    return $rslt;
}

$backlinks = array();
if (sizeof($user_backlinks) != 0) {
    foreach($user_backlinks as $backlink) {
        switch( $backlink['status'] ) {
            case 1:
                $status = 'Found';
                break;
            case 2:
                $status = 'Not found';
                break;
            default:
                $status = 'Not processed yet';
        }

        $nofollow = '';
        if ( $backlink['status'] == 1 ) {
            $nofollow = ( $backlink['nofollow'] == 1) ? 'Nofollow' : 'Follow';
        }

        $backlinks[] = [
            'URL' => $backlink['backlink_url'],
            'Backlink URL' => $backlink['backlink_link_url'],
            'Status' => $status,
            'Nofollow' => $nofollow,
            'Anchor' => $backlink['anchor_text'],
            'Domain' => $backlink['domain'],
            'Updated' => ($backlink['time_checked'] !=null ? date('d.m.Y H:i', $backlink['time_checked']) : null)
        ];
    }
} else {
    $backlinks['result'] = 'Nothing found';
}
$backlinks['result'] = 'Nothing found';

function array2csv(array &$array) {
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    fputcsv($df, array_keys(reset($array)));
    foreach ($array as $row) {
        fputcsv($df, $row);
    }
    fclose($df);
    return ob_get_clean();
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

download_send_headers("data_export_" . date("Y-m-d") . ".csv");
echo array2csv($backlinks);
die();