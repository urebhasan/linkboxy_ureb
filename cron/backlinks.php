<?php
set_time_limit(0);
ini_set('max_execution_time', 0);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../init.php');

$date = new DateTime();

use Curl\Curl;
use Curl\MultiCurl;

//mysqli_report(MYSQLI_REPORT_ALL);
$stmtU = $common->mysqli->prepare(
    "SELECT users_websites.user_id as user_id" .
        " FROM users " .
        " LEFT JOIN users_websites ON users.id = users_websites.user_id " .
        " LEFT JOIN users_backlinks on users_backlinks.domain_id = users_websites.id " .
        " LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id " .
        " WHERE users_backlinks.url IS NOT NULL " .
        "GROUP BY users_websites.user_id order by users_websites.user_id"
);
if (!$stmtU->execute()) {
    return false;
}
$resultU = $stmtU->get_result();
$stmtU->close();

$rsltU = $resultU->fetch_all(MYSQLI_ASSOC);
foreach ($rsltU as $elem) {
    $users[] = $elem['user_id'];
}


foreach ($users as $user_id) {

    $sql =
        "SELECT users.email as user_email, users_websites.user_id as user_id, users_websites.url as domain, users_websites.id as domain_id," .
        " users_backlinks.url as check_in, users_backlinks.id as backlink_id, users_backlinks_link.status as backlink_status, users_backlinks_link.anchor_text," .
        " users_backlinks_link.nofollow,users_backlinks_link.url as backlink_link_url, users_backlinks_link.id as backlink_link_id, users_backlinks.time_checked" .
        " FROM users LEFT JOIN users_websites ON users.id = users_websites.user_id " .
        " LEFT JOIN users_backlinks on users_backlinks.domain_id = users_websites.id " .
        " LEFT JOIN users_backlinks_link on users_backlinks_link.backlink_id = users_backlinks.id " .
        " WHERE users.id = " . $user_id . " AND users_backlinks.url IS NOT NULL " .
        " GROUP by users_backlinks.id,users.email,users_websites.user_id,users_websites.url,users_backlinks_link.url,users_backlinks_link.id " .
        " ORDER by users_backlinks.time_added,users_backlinks_link.time_added DESC;";

    $stmt = $common->mysqli->prepare($sql);

    if ($stmt === false) {
        continue;
    }
    if (!$stmt->execute()) {
        continue;
    }

    $result = $stmt->get_result();

    if (is_null($result)) {
        continue;
    }

    $stmt->close();
    $rslt = $result->fetch_all(MYSQLI_ASSOC);


    $emailQ = [];

    //enable multi_curl
    $multi_curl = new MultiCurl();
    $backlinksForCheckIn = array();
    $backlinksForCheckInChk = array();
    $backlinksForDomain = array();
    $backlinksForBLID = array();
    $backlinksForTXT = array();
    //$backlinksForNofollow = array();

    foreach ($rslt as $resource) {
        $check_in = $resource['check_in'];
        //this should never happen - if do so than integrity of DB is corrupted
        if (empty($check_in)) {
            continue;
        }

        //filter html data for domains
        if (
            array_key_exists($check_in, $backlinksForDomain) &&
            array_key_exists($check_in, $backlinksForBLID) &&
            array_key_exists($check_in, $backlinksForTXT)
            /* (array_key_exists($check_in, $backlinksForTXT) ||
                array_key_exists($check_in, $backlinksForNofollow) ) */
        ) {
            if (
                in_array($resource['domain'], $backlinksForDomain[$check_in]) &&
                in_array($resource['backlink_id'], $backlinksForBLID[$check_in]) &&
                in_array($resource['anchor_text'], $backlinksForTXT[$check_in])
                /* (in_array($resource['backlink_id'], $backlinksForTXT[$resource['anchor_text']]) ||
                     in_array($resource['backlink_id'], $backlinksForTXT[$resource['nofollow']])) */
            ) {
                continue;
            }
        }

        $backlinksForDomain[$check_in][] = $resource['domain'];
        $backlinksForBLID[$check_in][] = $resource['backlink_id'];
        $backlinksForTXT[$check_in][] = $resource['anchor_text'];
        //$backlinksForNofollow[$check_in][] = $resource['nofollow'];
        //add resource but dont call multiple urls
        $backlinksForCheckIn[strtolower($check_in)][] = $resource;
        //get html from check_in url only once
        if (array_key_exists($check_in, $backlinksForCheckInChk)) {
            continue;
        }
        $backlinksForCheckInChk[$check_in][] = $resource['domain'];
        // var_dump($check_in);
        $curl = new Curl();
        $curl->setOpt(CURLOPT_HTTPGET, 1);
        $curl->setOpt(CURLOPT_URL, strtolower($check_in));
        $curl->setOpt(CURLOPT_DNS_CACHE_TIMEOUT, 2);
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $curl->setOpt(CURLOPT_HEADER, 1);
        $curl->setOpt(CURLOPT_POST, 0);
        $curl->setOpt(CURLOPT_FAILONERROR, 1);
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
        $curl->setOpt(CURLOPT_CONNECTTIMEOUT, 120);
        $curl->setOpt(CURLOPT_TIMEOUT, 400);
        $curl->setOpt(CURLOPT_MAXREDIRS, 10);
        $curl->setOpt(CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $curl->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $curl->setHeader('Connection', 'keep-alive');
        $curl->setHeader('Upgrade-Insecure-Requests', '1');
        $curl->setHeader('User-Agent', 'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US);');
        $curl->setUrl(strtolower($check_in));
        $multi_curl->addCurl($curl);
    }

    $multi_curl->success(function ($instance) use ($backlinksForCheckIn, $backlinksForDomain, $common) {
        $iurl = $instance->url;
        $backLinksPerDomain = findBacklinksDetails($instance->response, $backlinksForCheckIn[$iurl], $backlinksForDomain[$iurl]);
        $reso = $backlinksForCheckIn;
        $u_email = null;

        //init if domain is added without backlinks
        foreach ($reso as $ind => $rsc) {
            foreach ($rsc as $rkey => $resource) {
                if (is_null($u_email)) {
                    $u_email = $resource['user_email'];
                }
                if ($resource['check_in'] !== $iurl) {
                    continue;
                }
                foreach ($backLinksPerDomain[$resource['domain']] as $key => $bck_link) {
                    if ($resource['backlink_id'] == $bck_link['id']) {
                        if (is_null($resource['backlink_link_id'])) {
                            $common->insertNewBacklinkLink($bck_link['href'], $bck_link['anchor_text'], ($bck_link['nofollow'] == true ? 1 : null), $instance->httpStatusCode, $bck_link['id']);
                            $common->mailQueue($u_email, 4, $resource, $bck_link, $iurl);
                            $rsc[$rkey]['nofollow'] = ($bck_link['nofollow'] == 1) ? 1 : null;
                            $rsc[$rkey]['backlink_link_url'] = $bck_link['href'];
                            $rsc[$rkey]['anchor_text'] = $bck_link['anchor_text'];
                            $rsc[$rkey]['backlink_status'] = 1;

                            $reso[$ind] = $rsc;

                            $stmt = $common->mysqli->prepare("UPDATE users_backlinks SET time_checked = ?, status = ? WHERE id = ? LIMIT 1");
                            $currentTime = time();
                            $status = 1;
                            $stmt->bind_param("iii", $currentTime, $status, $resource['backlink_id']);
                            if (!$stmt->execute()) {
                                return false;
                            }
                            $result = $stmt->get_result();
                            $stmt->close();
                            continue 2;
                        }
                    }
                }
            }
        }
        //after init
        //skip all resources/backlinks which are not changed
        foreach ($reso as $ind => $rsc) {
            foreach ($rsc as $rkey => $resource) {
                if ($resource['check_in'] !== $iurl) {
                    continue;
                }
                foreach ($backLinksPerDomain[$resource['domain']] as $key => $bck_link) {
                    if ($resource['backlink_id'] == $bck_link['id']) {
                        //find identical - and remove from action
                        if ($resource['anchor_text'] != $bck_link['anchor_text'] || $resource['nofollow'] != $bck_link['nofollow']  || $resource['backlink_status'] == 2) {
                            continue;
                        } else {
                            unset($backLinksPerDomain[$resource['domain']][$key]);
                            unset($rsc[$rkey]);
                        }
                    }
                }
            }
            $reso[$ind] = $rsc;
        }
        //after filter

        foreach ($reso as $ind => $rsc) {
            foreach ($rsc as $rkey => $resource) {
                if ($resource['check_in'] !== $iurl) {
                    continue;
                }
                $found = false;
                $founded_arr = array();
                foreach ($backLinksPerDomain[$resource['domain']] as $key => $bck_link) {
                    if ($resource['backlink_id'] == $bck_link['id']) {
                        //check if exist - 0
                        if (
                            $resource['backlink_link_url'] != $bck_link['href'] ||
                            ($resource['backlink_link_url'] == $bck_link['href'] &&
                                $resource['anchor_text'] != $bck_link['anchor_text'] /*&&
                                $resource['nofollow'] != $bck_link['nofollow']*/)
                        ) {
                            //not exists anymore
                            $found = false;
                            //send mail
                        } else {
                            $founded_arr[] = $key;
                            $found = true;

                            if ($resource['backlink_status'] == 2) {
                                $common->updateBacklink($resource['backlink_link_id'], '1', $bck_link['anchor_text'], $bck_link['nofollow'], $instance->httpStatusCode, $resource['backlink_link_url']);
                                unset($rsc[$rkey]);
                                //EMAIL backlink reactivated - 6
                                //recipient_mail, sender, recipient, headers, body, status
                                $common->mailQueue($resource['user_email'], 6, $resource, $bck_link, $iurl);
                                foreach ($founded_arr as $k) {
                                    unset($backLinksPerDomain[$resource['domain']][$k]);
                                };
                            }

                            continue 2;
                        }
                    } else {
                        $found = false;
                    }
                }

                if (!$found) {
                    //backlink(s) not founded on domain
                    //echo 'Not found ' . $resource['anchor_text'] . ' for ' . $resource['domain'];
                    //set 2 if backlink was not found
                    if ($resource['backlink_status'] != 2) {
                        $common->updateBacklink($resource['backlink_link_id'], '2', $resource['anchor_text'], null, $instance->httpStatusCode, $resource['backlink_link_url']);
                        //EMAIL backlink is down or anchor text is changed - 5
                        if ($resource['backlink_status'] == 1) {
                            $common->mailQueue($resource['user_email'], 5, $resource, null, $iurl);
                        }
                        $rsc[$rkey]['nofollow'] = ($resource['nofollow'] == 1) ? 1 : null;
                        $rsc[$rkey]['backlink_link_url'] = $resource['backlink_link_url'];
                        $rsc[$rkey]['anchor_text'] = $resource['anchor_text'];
                        $rsc[$rkey]['backlink_status'] = 2;
                        $reso[$ind] = $rsc;
                    }
                }
            }
            $reso[$ind] = $rsc;
        }
        //after checking backlinks
        /* var_dump($reso);
            var_dump($backLinksPerDomain); */

        foreach ($reso as $rsc) {
            foreach ($rsc as $rkey => $resource) {
                if ($resource['check_in'] !== $iurl) {
                    continue;
                }
                if ($resource['backlink_status'] == 2) {
                    continue;
                }
                foreach ($backLinksPerDomain[$resource['domain']] as $key => $bck_link) {
                    if ($resource['backlink_id'] == $bck_link['id']) {
                        //find with text changed - 1
                        if ($resource['anchor_text'] != $bck_link['anchor_text']) {
                            //update anchor text
                            $common->updateBacklink($resource['backlink_link_id'], '1', $bck_link['anchor_text'], ($resource['nofollow'] == 1) ? 1 : null, $instance->httpStatusCode, $resource['backlink_link_url']);
                            //send mail
                            //EMAIL anchor text edited - 1
                            $common->mailQueue($resource['user_email'], 1, $resource, $bck_link, $iurl);
                        }
                        //find with nofollow changed - 3
                        $rnf = ($resource['nofollow'] == 1) ? 1 : null;
                        $bnf = ($bck_link['nofollow'] == 1) ? 1 : null;
                        if ($rnf !== $bnf) {
                            //update nofollow
                            $common->updateBacklink($resource['backlink_link_id'], '1', ($resource['anchor_text'] != $bck_link['anchor_text']) ? $bck_link['anchor_text'] : $resource['anchor_text'], $bck_link['nofollow'], $instance->httpStatusCode, $resource['backlink_link_url']);
                            //send mail
                            //EMAIL nofollow edited - 3
                            $common->mailQueue($resource['user_email'], 3, $resource, $bck_link, $iurl);
                        }
                        if ($rnf !== $bnf || $resource['anchor_text'] != $bck_link['anchor_text']) {
                            unset($backLinksPerDomain[$resource['domain']][$key]);
                            continue 2;
                        }
                    }
                }
            }
        }
        //after checking props
        if (empty($backLinksPerDomain)) return false;
        //find new if exist - 4
        foreach ($backLinksPerDomain as $rsrc) {
            foreach ($rsrc as $key => $bck_link) {
                $common->insertNewBacklinkLink($bck_link['href'], $bck_link['anchor_text'], ($bck_link['nofollow'] == true ? 1 : null), $instance->httpStatusCode, $bck_link['id']);
                //EMAIL new backlink found - 4
                $common->mailQueue($u_email, 4, $bck_link, $bck_link, $iurl, $bck_link);
            }
        }
        //after adding existing links not existing in db            
        //site down - 5
    });

    $multi_curl->error(function ($instance) use ($backlinksForCheckIn) {
        $logInfo  =  date('d/m/Y H:i:s', time()) . ' Call to "' . $instance->url . '" was unsuccessful.';
        $logInfo .= ' Error code: ' . $instance->errorCode;
        $logInfo .= ' Error message: ' . $instance->errorMessage . "\n";
        file_put_contents(__DIR__ . '/../CurlErrorLog.log', $logInfo, FILE_APPEND);
        //site down - 5
        //send mail :: $instance->errorMessage
        //send to:
        //$backlinksForCheckIn[$instance->url][0]['user_email'];
        //$res = $backlinksForCheckIn[$instance->url][0];
        //$checkDomain = str_replace(['https://www', 'http://www', 'https://', 'http://', 'www.'], ['', '', '', '', ''], strtolower($res['domain']));
        //Link is down for some reason
        //$emailQ[$res['user_id']]['5'][] = ['domain' => $checkDomain, 'backlink' => $res['check_in']];
    });

    $multi_curl->complete(function ($instance) {
        //echo 'call to "' . $instance->url . '" completed.' . "\n";
    });

    $multi_curl->start();
}

function findBacklinksDetails($html, $backlinks4checkin, $backlinks4domain)
{
    $doc = new DOMDocument;
    @$doc->loadHTML($html);
    $xpath = new DOMXPath($doc);

    $blCollection = null;
    /* var_dump($backlinks4checkin);
            var_dump($backlinks4domain); */
    if (empty($backlinks4checkin)) return null;
    foreach ($backlinks4checkin as $i => $backlink) {
        $backlinksFound = [];
        //var_dump($backlink);
        //$checkDomain = str_replace(['https://www', 'http://www', 'https://', 'http://', 'www.'], ['', '', '', '', ''], strtolower($backlink['backlink_link_url']));
        $checkDomain = str_replace(['https://www', 'http://www', 'https://', 'http://', 'www.'], ['', '', '', '', ''], strtolower($backlink['domain']));
        $nofollowFlag = false;
        $nofollow = $backlink['nofollow'];
        $linksFound = $xpath->query(".//a[contains(translate(@href, 'ABCDEFGHJIKLMNOPQRSTUVWXYZ', 'abcdefghjiklmnopqrstuvwxyz'),'" . $checkDomain . "')]");

        foreach ($linksFound as $i => $link) {
            $href = $link->getAttribute('href');
            $rel = $link->getAttribute('rel');
            $anchorText = $link->nodeValue;
            $nofollowFlag = false;

            if (strpos($rel, "nofollow") !== false) {
                $nofollowFlag = true;
            }
            $backlinksFound[] = ['href' => $href, 'anchor_text' => $anchorText, 'nofollow' => $nofollowFlag, 'id' => $backlink['backlink_id']];
        }
        $blCollection[$backlink['domain']] = $backlinksFound;
    }
    return $blCollection;
}
