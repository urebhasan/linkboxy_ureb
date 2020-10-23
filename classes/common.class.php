<?php
class common
{
    public $server_dir;
    public $domain;
    public $mysqli;
    public $date;
    public $userAgent = 'Mozilla/5.0 (X11; U; Linux i686; es-ES; rv:1.9.0.11) Gecko/2009061319 Iceweasel/3.0.11 (Debian-3.0.11-1)';

    function __construct($config)
    {
        @session_start();
        $this->mysqli = new mysqli($config['db_host'], $config['db_username'], $config['db_pass'], $config['db_name']);
        $this->mysqli->query("SET NAMES 'utf8'");
        $this->server_dir = $config['server_dir'];
        $this->domain = $config['site_url'];
        $this->date = new DateTime();
    }

    public function getEmailById($id)
    {
        $stmt = $this->mysqli->prepare("SELECT id,subject,text,email,send from emails_to_send WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            return false;
        }
        $result = $stmt->get_result();
        $stmt->close();
        $rslt = $result->fetch_array(MYSQLI_ASSOC);

        return ($rslt ? $rslt : false);
    }

    public function sendMail($to, $subject, $text)
    {
        if (
            strpos($text, 'Anchor changed:') !== false ||
            strpos($text, 'New backlink found:') !== false ||
            strpos($text, 'Backlink not found:') !== false ||
            strpos($text, 'Backlinks not found:') !== false ||
            strpos($text, 'Nofollow attribute seen:') !== false ||
            strpos($text, 'Anchor text edited on backlink:') !== false
        ) {
            return false;
        }
        $stmt = $this->mysqli->prepare("INSERT INTO emails_to_send(subject, text, email, time) VALUES(?,?,?,?)");
        $currentTime = time();


        $stmt->bind_param("sssi", $subject, $text, $to, $currentTime);
        if (!$stmt->execute()) {
            return false;
        }
        $result = $stmt->get_result();
        $insertId = $stmt->insert_id;
        $stmt->close();

        exec('/usr/bin/php70 ' . $this->server_dir . 'cron/sendEmail.php insert=' . $insertId . ' > /dev/null &');

        return true;
    }

    public function mailQueue($recipient_mail, $type, $resource, $bl, $blink, $hlpr = null)
    {
        $type = intval($type);
        $status = '';
        $sender = 'alerts@linkboxy.com';
        $recipient = null;
        $headers = null;
        $backlinksText__ = '';
        $backlinksText_ = '';
        $at = 'Anchor text';
        switch ($type) {
            case 0:
                $status = 'Backlinks not found';
                break;
            case 1:
                $status = 'Anchor text edited on backlink';
                $at = 'New anchor text';
                break;
            case 3:
                $status = 'Nofollow attribute seen on backlink';
                break;
            case 4:
                $status = 'New backlink found';
                break;
            case 5:
                //return;
                $status = 'Link is down';
                break;
            case 6:
                $status = 'Backlink reactivated';
                break;
        }
        $blAT =  is_null($bl) ? $hlpr['anchor_text'] : filter_var($bl['anchor_text'], FILTER_SANITIZE_STRING);
        $rscAT = filter_var($resource['anchor_text'], FILTER_SANITIZE_STRING);

        $a = (($type == 1 || $type == 4) ? $blAT : $rscAT);

        $backlinksText__ = "<span style=\"color: #ffffff; font-size: 14px\">" . $status . "</span>: <span style=\"color:lightblue; font-size: 14px\">" . $blink . "</span><br/><span style=\"color: #ffffff; font-size: 14px\">For your domain: </span><span style=\"color:lightblue; font-size: 14px\">" . $bl['href'] . "</span><br/><span style=\"color: #ffffff; font-size: 14px\">" . $at . "</span>: <span style=\"color:#ffffff; font-size: 14px\"><b>" . $a . "</b></span>";

        if (!array_key_exists('domain', $resource)) {
            if ($type != 1 && $type != 4) {
                $a = empty($blAT) ? $rscAT : $blAT;
            }
            $backlinksText_ = "<span style=\"color: #ffffff; font-size: 14px\">" . $status . "</span>: <span style=\"color:lightblue; font-size: 14px\">" . $blink . "</span><br/><span style=\"color: #ffffff; font-size: 14px\">For your domain: </span><span style=\"color:lightblue; font-size: 14px\">" . empty($bl['href']) ? $resource['href'] : $bl['href'] . "</span><br/><span style=\"color: #ffffff; font-size: 14px\">" . $at . "</span>: <span style=\"color:#ffffff; font-size: 14px\"><b>" . $a . "</b></span>";
        } else {
            $backlinksText_ = "<span style=\"color: #ffffff; font-size: 14px\">" . $status . "</span>: <span style=\"color:lightblue; font-size: 14px\">" . $resource['check_in'] . "</span><br/><span style=\"color: #ffffff; font-size: 14px\">For your domain: </span><span style=\"color:lightblue; font-size: 14px\">" . $resource['domain'] . "</span><br/><span style=\"color: #ffffff; font-size: 14px\">" . $at . "</span>: <span style=\"color:#ffffff; font-size: 14px\"><b>" . $a . "</b></span>";
        }
        $backlinksText__ = (strlen($backlinksText_) > strlen($backlinksText__)) ? $backlinksText_ : $backlinksText__;

        $backlinksText__ = "<div style=\"font-family: inherit; text-align: left\"><br></div><div style=\"font-family: inherit; text-align: left\"><span style=\"color: #ffffff; font-size: 14px\">" . $backlinksText__ . "</span></div>";

        $stmt = $this->mysqli->prepare("INSERT INTO mail_queue(recipient_mail, sender, recipient, headers, body, status) VALUES(?,?,?,?,?,?)");
        $stmt->bind_param("sssssi", $recipient_mail, $sender, $recipient, $headers, $backlinksText__, $type);

        if (!$stmt->execute()) {
            return false;
        }
        $result = $stmt->get_result();
        $insertId = $stmt->insert_id;
        $stmt->close();
    }

    public function getContent($url)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 25);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: ' . $this->userAgent
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!$ch) {
            return false;
        }
        $result = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($result, 0, $header_len);
        $content = substr($result, $header_len);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        file_put_contents($this->server_dir . 'curl_error.txt', var_export($error, true));
        curl_close($ch);
        return (!$error ? ['httpCode' => $httpCode, 'content' => $content] : false);
    }


    public function insertNewBacklinkLink($backlinkUrl, $anchor, $nofollow, $http, $backlinkParentId)
    {
        // HTTP:
        // 404 - 0
        // 200 - 1
        // 302 - 2
        // 301 - 3
        $httpCodes = ['404' => 0, '200' => 1, '301' => 2, '301' => 3];
        $http = (array_key_exists($http, $httpCodes) ? $httpCodes[$http] : null);
        try {
            $stmt = $this->mysqli->prepare("INSERT into users_backlinks_link(backlink_id,url,time_checked,time_added,anchor_text,status,nofollow,http_status) VALUES(?,?,?,?,?,?,?,?)");
            $currentTime = time();
            $status = 1;
            $nofollow = ($nofollow == 1) ? 1 : null;
            $stmt->bind_param("isiisiii", $backlinkParentId, $backlinkUrl, $currentTime, $currentTime, $anchor, $status, $nofollow, $http);
            if (!$stmt->execute()) {
                return false;
            }
        } catch (Exception $e) {
            file_put_contents($this->server_dir . 'staging_mysqli.txt', $this->date->format('Y-m-d H:i:s') . "\n" .  $e->getMessage() . "\n", FILE_APPEND);
        } finally {
            $result = $stmt->get_result();
            $stmt->close();
        }


        return true;
    }

    public function updateBacklink($backlinkId, $status, $anchor, $nofollow, $http, $backlinkUrl)
    {
        // HTTP:
        // 404 - 0
        // 200 - 1
        // 302 - 2
        // 301 - 3
        $httpCodes = ['404' => 0, '200' => 1, '301' => 2, '301' => 3];
        $http = (array_key_exists($http, $httpCodes) ? $httpCodes[$http] : null);

        $nofollow = ($nofollow == 1) ? 1 : null;
        // Status:
        // 0 - not checked yet
        // 1 - found
        // 2 - not found
        // Если статус - 2, то обновляем все по domain_id
        try {
            if ($status == 2) {
                $stmt = $this->mysqli->prepare("UPDATE users_backlinks_link SET time_checked = ?, status = ?, anchor_text = ?, nofollow = ?, http_status = ? WHERE id = ? LIMIT 1");
                $currentTime = time();
                $stmt->bind_param("iisiii", $currentTime, $status, $anchor, $nofollow, $http, $backlinkId);
                if (!$stmt->execute()) {
                    return false;
                }
            } else {
                //why do we have here exact same code?????
                $stmt = $this->mysqli->prepare("UPDATE users_backlinks_link SET time_checked = ?, status = ?, anchor_text = ?, nofollow = ?, http_status = ? WHERE id = ? LIMIT 1");
                $currentTime = time();
                $stmt->bind_param("iisiii", $currentTime, $status, $anchor, $nofollow, $http, $backlinkId);
                if (!$stmt->execute()) {
                    return false;
                }
            }
        } catch (Exception $e) {
            file_put_contents($this->server_dir . 'staging_mysqli.txt', $this->date->format('Y-m-d H:i:s') . "\n" .  $e->getMessage() . "\n", FILE_APPEND);
        } finally {
            $result = $stmt->get_result();
            $stmt->close();
        }

        return true;
    }

    /**
     * throws success and message as json array
     * @param  string $text message text
     */
    public function throwJsonSuccess($text)
    {
        exit(json_encode(['success' => true, 'text' => $text], JSON_UNESCAPED_UNICODE));
    }

    /**
     * throws error message encoded in json array
     * @param  string $text error text
     */
    public function throwJsonError($text)
    {
        exit(json_encode(['error' => true, 'text' => $text], JSON_UNESCAPED_UNICODE));
    }
}
