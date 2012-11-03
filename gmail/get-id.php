<?php

require_once("oauth2.php");

$email = $_GET['email'];
$access_token = $_GET['access_token'];
$subject = $_GET['subj'];

header('Content-type: text/plain');

if ($email && $access_token) {
    $imap = tryImapLogin($email, $access_token);
    if ($imap) {
        $storage = new Zend_Mail_Storage_Imap($imap);

        $storage->selectFolder("[Gmail]/Drafts");

        $found = false;

        for($i = 1; $i <= $storage->countMessages(); $i++) {
            $msg = $storage->getMessage($i);
            if ($msg->subject == $subject) {
                $headers = $msg->getHeaders();
                echo $headers["message-id"];
                $found = true;
                break;
            }
        }

        if (!$found) {
            echo 'null';
        }
    }
}
