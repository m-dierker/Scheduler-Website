<?php

require_once("config.php");
require_once("lib/facebook-php/facebook.php");

$config = array();
$config['appId'] = FACEBOOK_APP_ID;
$config['secret'] = FACEBOOK_APP_SECRET;
$config['fileUpload'] = false;

$facebook = new Facebook($config);

$code = $_REQUEST["code"];

$my_url = "http://schedule.eatcumtd.com/auth.php";

if (empty($code)) {
    // Send to login URL
    $_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
     $dialog_url = "https://www.facebook.com/dialog/oauth?client_id="
       . FACEBOOK_APP_ID . "&redirect_uri=" . urlencode($my_url) . "&state="
       . $_SESSION['state'] . "&scope=xmpp_login";

     echo("<script> top.location.href='" . $dialog_url . "'</script>");
}

if ($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state'])) {
    // state variable matches
    $token_url = "https://graph.facebook.com/oauth/access_token?"
       . "client_id=" . FACEBOOK_APP_ID . "&redirect_uri=" . urlencode($my_url)
       . "&client_secret=" . FACEBOOK_APP_SECRET . "&code=" . $code;

     $response = file_get_contents($token_url);
     $params = null;
     parse_str($response, $params);

     $_SESSION['access_token'] = $params['access_token'];

     $graph_url = "https://graph.facebook.com/me?access_token=" . $params['access_token'];

     $user = json_decode(file_get_contents($graph_url));
     echo ("Hello " . $user->name);

} else {
    die("The state does not match. CSRF error.");
}
