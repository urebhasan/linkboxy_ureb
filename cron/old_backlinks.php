<?php
set_time_limit(0);
require(__DIR__ . '/../init.php');

file_put_contents($common->server_dir . 'alive.txt', time());
ini_set('memory_limit', '756M');

function strposArray($haystack, $needle) {
    if (!is_array($needle)) $needle = array($needle);
    foreach ($needle as $need) {
        if (strpos($haystack, $need) !== false) return true;
    }
    return false;
}



$time = strtotime('-6hours');
// $stmt = $common->mysqli -> prepare("SELECT users.email as user_email, users_websites.user_id as user_id, users_websites.url as domain, users_websites.id as domain_id, users_backlinks.url as check_in, users_backlinks.id as backlink_id, users_backlinks_link.status as backlink_status, users_backlinks_link.anchor_text, users_backlinks_link.nofollow,users_backlinks_link.url as backlink_link_url, users_backlinks_link.id as backlink_link_id, users_backlinks.time_checked FROM users_websites
// LEFT JOIN users_backlinks on users_backlinks.domain_id = users_websites.id
// LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id
// LEFT JOIN users on users.id = users_websites.user_id
// WHERE users_backlinks.time_checked<=".$time." or users_backlinks.time_checked IS NULL GROUP by users_backlinks.id,users.email,users_websites.user_id,users_websites.url,users_backlinks_link.url,users_backlinks_link.id ORDER by users_backlinks.time_added,users_backlinks_link.time_added DESC");
// Запрашиваем users_websites
//WHERE users_backlinks.time_checked<=" . $time . " or users_backlinks.time_checked IS NULL 
/*$stmt = $common->mysqli->prepare("SELECT users.email as user_email, users_websites.user_id as user_id, users_websites.url as domain, users_websites.id as domain_id, users_backlinks.url as check_in, users_backlinks.id as backlink_id, users_backlinks_link.status as backlink_status, users_backlinks_link.anchor_text, users_backlinks_link.nofollow,users_backlinks_link.url as backlink_link_url, users_backlinks_link.id as backlink_link_id, users_backlinks.time_checked FROM users_websites
LEFT JOIN users_backlinks on users_backlinks.domain_id = users_websites.id
LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id
LEFT JOIN users on users.id = users_websites.user_id
GROUP by users_backlinks.id,users.email,users_websites.user_id,users_websites.url,users_backlinks_link.url,users_backlinks_link.id ORDER by users_backlinks.time_added,users_backlinks_link.time_added DESC");*/
$stmt = $common->mysqli->prepare(
    "SELECT users.email as user_email, users_websites.user_id as user_id, users_websites.url as domain, users_websites.id as domain_id," .
    " users_backlinks.url as check_in, users_backlinks.id as backlink_id, users_backlinks_link.status as backlink_status, users_backlinks_link.anchor_text," .
    " users_backlinks_link.nofollow,users_backlinks_link.url as backlink_link_url, users_backlinks_link.id as backlink_link_id, users_backlinks.time_checked" .
    " FROM users LEFT JOIN users_websites ON users.id = users_websites.user_id " .
    " LEFT JOIN users_backlinks on users_backlinks.domain_id = users_websites.id " .
    " LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id " .
    " WHERE users_backlinks.time_checked<=" . $time . " or users_backlinks.time_checked IS NULL " .
    "GROUP by users_backlinks.id,users.email,users_websites.user_id,users_websites.url,users_backlinks_link.url,users_backlinks_link.id " .
    "ORDER by users_backlinks.time_added,users_backlinks_link.time_added DESC;"
);
if (!$stmt->execute()) return false;
$result = $stmt->get_result();
$stmt->close();
$rslt = $result->fetch_all(MYSQLI_ASSOC);

$emailQ = [];
$start = microtime(true);

$alreadyCheckedBacklinks = [];
foreach ($rslt as $backlink) {
    if (array_key_exists($backlink['check_in'], $alreadyCheckedBacklinks)) {
        continue;
    }

    $domainBacklinks = [];
    foreach ($rslt as $count) {
        if ($count['backlink_id'] != $backlink['backlink_id']) continue;
        $domainBacklinks[] = ['id' => $count['backlink_id'], 'status' => $count['backlink_status'], 'anchor' => $count['anchor_text'], 'nofollow' => $count['nofollow'], 'backlink_link_url' => $count['backlink_link_url'], 'backlink_link_id' => $count['backlink_link_id']];
    }

    $backlinksCount = sizeof($domainBacklinks);

    //@TODO SQL query wrong, return empty results sometimes, maybe we need inner join
    //@TODO This is temporary fix
    if (empty($backlink['check_in'])) {
        continue;
    }

    if (!$content = $common->getContent($backlink['check_in'])) {
        continue;
        $emailQ[$backlink['user_id']]['5'][] = ['domain' => $checkDomain, 'backlink' => $backlink['check_in']];
    }

    $checkDomain = str_replace(['https://www', 'http://www', 'https://', 'http://', 'www.'], ['', '', '', '', ''], strtolower($backlink['domain']));


    $nofollowFlag = false;
    $nofollow = $backlink['nofollow'];

    $doc = new DOMDocument;
    @$doc->loadHTML($content['content']);
    $xpath = new DOMXPath($doc);
    $linksFound = $xpath->query(".//a[contains(translate(@href, 'ABCDEFGHJIKLMNOPQRSTUVWXYZ', 'abcdefghjiklmnopqrstuvwxyz'),'" . $checkDomain . "')]");
    $backlinksFound = [];
    foreach ($linksFound as $i => $link) {
        $href = $link->getAttribute('href');
        $rel = $link->getAttribute('rel');
        $anchorText = $link->nodeValue;
        $nofollowFlag = false;

        if (strpos($rel, "nofollow") !== false) {
            $nofollowFlag = true;
        }

        $backlinksFound[] = ['href' => $href, 'anchor_text' => $anchorText, 'nofollow' => $nofollowFlag];
    }
    $notFoundChecked = [];
    foreach ($backlinksFound as $y => $found) {
        $notfound = true;
        foreach ($domainBacklinks as $i => $domainBacklink) {
            if ($domainBacklink['backlink_link_url'] == $found['href']) {
                $notfound = false;
                if ($domainBacklink['anchor'] != $found['anchor_text']) {
                    $emailQ[$backlink['user_id']]['1'][] = ['domain' => $checkDomain, 'backlink' => $backlink['check_in'], 'backlinkUrl' => $domainBacklink['backlink_link_url'], 'oldAnchor' => $backlink['anchor_text'], 'newAnchor' => $found['anchor_text']];
                }

                $nofollow = null;
                if ($found['nofollow'] == true) {
                    $nofollow = 1;
                    if ($domainBacklink['nofollow'] == null and $domainBacklink['status'] != '2') {
                        $emailQ[$backlink['user_id']]['3'][] = ['domain' => $checkDomain, 'backlink' => $backlink['check_in'], 'backlinkUrl' => $domainBacklink['backlink_link_url'], 'oldAnchor' => $backlink['anchor_text'], 'newAnchor' => $found['anchor_text']];
                    }
                }
                $common->updateBacklink($domainBacklink['backlink_link_id'], '1', $found['anchor_text'], $nofollow, $content['httpCode'], $domainBacklink['backlink_link_url']);
                unset($domainBacklinks[$i]);
                break;
            }
        }
        if ($notfound == true) {
            $notFoundChecked[] = $found;
        }

    }

    foreach ($notFoundChecked as $newBacklink) {
        $common->insertNewBacklinkLink($newBacklink['href'], $newBacklink['anchor_text'], ($newBacklink['nofollow'] == true ? 1 : null), $content['httpCode'], $backlink['backlink_id']);
    }
    foreach ($domainBacklinks as $notFoundBL) {
        if ($notFoundBL['backlink_link_url'] == null) continue;
        if ($backlink['backlink_status'] == 0 or $backlink['backlink_status'] == 1) {
            $emailQ[$backlink['user_id']]['0'][] = ['domain' => $checkDomain, 'backlink' => $backlink['check_in'], 'backlinkUrl' => $notFoundBL['backlink_link_url'], 'oldAnchor' => $backlink['anchor_text'], 'newAnchor' => $notFoundBL['anchor_text']];
        }
        $common->updateBacklink($notFoundBL['backlink_link_id'], '2', $notFoundBL['anchor'], null, $content['httpCode'], $notFoundBL['backlink_link_url']);

    }

    if ($linksFound->length == 0) {
        $status = 2;
    } elseif ($linksFound->length > 0) {
        $status = 1;
    }

    $stmt = $common->mysqli->prepare("UPDATE users_backlinks SET time_checked = ?, status = ? WHERE id = ? LIMIT 1");
    $currentTime = time();
    $stmt->bind_param("iii", $currentTime, $status, $backlink['backlink_id']);
    if (!$stmt->execute()) return false;
    $result = $stmt->get_result();
    $stmt->close();

    $alreadyCheckedBacklinks[$backlink['check_in']] = true;
}


$currentType = null;
foreach ($emailQ as $user_id => $types) {
    $text = null;
    foreach ($types as $type => $backlinks) {
        $at = 'Anchor text';
        if ($type == 0) {
            $status = 'Backlinks not found';
        } elseif ($type == 1) {
            $status = 'Anchor text edited on backlink';
            $at = 'New anchor text';
        } elseif ($type == 3) {
            $status = 'Nofollow attribute seen on backlink';
        } elseif ($type == 4) {
            $status = 'New backlink found';
        } elseif ($type == 5) {
            $status = 'Link is down';
        }

        $backlinksText = null;
        foreach ($backlinks as $backlink) {
            $a = ($type == 1 ? $backlink['newAnchor'] : $backlink['oldAnchor']);
            $backlinksText .= '<br/>--<br/><b>' . $status . ':</b> ' . $backlink['backlink'] . '<br/>For your domain: ' . $backlink['domain'] . '<br/>' . $at . ': ' . $a;
        }

        $text .= "<br/>--<br/>" . $backlinksText;
        $currentType = $type;
    }

    if ($user_info = $user->user_info_by_id($user_id)) {
        //$common->sendMail($user_info['email'], 'Backlinks notification', $text.'<br/>Thanks<br/>LinkBoxy.com');
        //exec('php /home/u361-zrkv5gswcb76/www/app.linkboxy.com/public_html/cron/sendEmailDirect.php "' . $user_info['email'] . '" "LinkBoxy.com Notification" "' . $text . '<br/>Thanks<br/>LinkBoxy.com" > /dev/null &');
    }
}

$time_elapsed_secs = microtime(true) - $start;

echo $time_elapsed_secs;