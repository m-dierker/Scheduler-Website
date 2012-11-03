<?php

require_once("oauth2.php");
require_once("Zend/Mail.php");
require_once 'Zend/Mail/Protocol/Smtp.php';

$email = $_GET['email'];
$access_token = $_GET['access_token'];
$id = str_replace(' ', '+', $_GET['id']);

header('Content-type: text/plain');

if ($email && $access_token) {
    $imap = tryImapLogin($email, $access_token);
    if ($imap) {
        $storage = new Zend_Mail_Storage_Imap($imap);

        $storage->selectFolder("[Gmail]/Drafts");

        $found = false;

        for($i = 1; $i <= $storage->countMessages(); $i++) {
            $msg = $storage->getMessage($i);
            $headers = $msg->getHeaders();
            var_dump($headers);
            if($headers["message-id"] == $id) {
                $transport = trySmtpLogin($email, $access_token);
                if ($transport) {
                    sendMessage($msg, $transport);
                }
                echo 'sent';
            } else {
                echo "\"" . $id . "\"\n";
                echo "\"" . $headers["message-id"] . "\"";
            }
        }

        if (!$found) {
            echo 'null';
        }
    }
}

function sendMessage(Zend_Mail_Part $msg, Zend_Mail_Protocol_Smtp $transport) {
    $mail = new Zend_Mail();

    $headers = $msg->getHeaders();
    $to = explode(",", $headers["to"]);
    foreach ($to as $x) {
        $x = trim($x);
        if ($pos = strpos($x, "<")) {
            $name = trim(substr($x, 0, $pos));
            $email = substr($x, $pos+1, strpos($x, ">") - $pos - 1);
            $mail->addTo($email, $name);
        } else {
            $mail->addTo($x);
        }
    }

    $from = trim($headers["from"]);
    if ($pos = strpos($from, "<")) {
        $name = trim(substr($from, 0, $pos));
        $email = substr($from, $pos+1, strpos($from, ">") - $pos - 1);
        $mail->setFrom($email, $name);
    } else {
        $mail->setFrom($from);
    }

    $mail->setSubject($headers["subject"]);
    $mail->setBodyText($msg->getContent());

    $mail->send($transport);


}
