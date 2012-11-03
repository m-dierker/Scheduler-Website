<?php
require_once 'Zend/Mail/Protocol/Imap.php';
require_once 'Zend/Mail/Storage/Imap.php';
require_once 'Zend/Oauth/Config.php';
require_once 'Zend/Oauth/Token.php';

/**
 * Builds an OAuth2 authentication string for the given email address and access
 * token.
 */
function constructAuthString($email, $accessToken) {
  return base64_encode("user=$email\1auth=Bearer $accessToken\1\1");
}

/**
 * Given an open IMAP connection, attempts to authenticate with OAuth2.
 *
 * $imap is an open IMAP connection.
 * $email is a Gmail address.
 * $accessToken is a valid OAuth 2.0 access token for the given email address.
 *
 * Returns true on successful authentication, false otherwise.
 */
function oauth2Authenticate($imap, $email, $accessToken) {
  $authenticateParams = array('XOAUTH2',
      constructAuthString($email, $accessToken));
  $imap->sendRequest('AUTHENTICATE', $authenticateParams);
  while (true) {
    $response = "";
    $is_plus = $imap->readLine($response, '+', true);
    if ($is_plus) {
      error_log("got an extra server challenge: $response");
      // Send empty client response.
      $imap->sendRequest('');
    } else {
      if (preg_match('/^NO /i', $response) ||
          preg_match('/^BAD /i', $response)) {
        error_log("got failure response: $response");
        return false;
      } else if (preg_match("/^OK /i", $response)) {
        return true;
      } else {
        // Some untagged response, such as CAPABILITY
      }
    }
  }
}

/**
 * Tries to login to IMAP and show inbox stats.
 */
function tryImapLogin($email, $accessToken) {
  /**
   * Make the IMAP connection and send the auth request
   */
  $imap = new Zend_Mail_Protocol_Imap('imap.gmail.com', '993', true);
  if (oauth2Authenticate($imap, $email, $accessToken)) {
    return $imap;
  } else {
    die('Failed to login');
    return null;
  }
}

function trySmtpLogin($email, $accessToken) {
  /**
   * Make the IMAP connection and send the auth request
   */

  $smtpInitClientRequestEncoded = getXoauthClientRequest('smtp.gmail.com', $email, $accessToken);
  $config = array('ssl' => 'ssl',
                  'port' => '465',
                  'auth' => 'xoauth',
                  'xoauth_request' => $smtpInitClientRequestEncoded);

  $imap = new Zend_Mail_Transport_Smtp('smtp.gmail.com', '465', $config);
  return $imap;
}

function getXoauthClientRequest($smtpUrl, $emailAddress, $accessToken) {
  $config = new Zend_Oauth_Config();

  $options = array(
      'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
      'version' => '1.0',
      'consumerKey' => '1007887650719-g8okt91gfd3mvg46aod9tns7a3k0r4gf.apps.googleusercontent.com',
      'callbackUrl' => 'http://schedule.eatcumtd.com/send-by-id.php',
      'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
      'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
      'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken',
      'consumerSecret' => 'mzMIlpXuxZmDEvE_I5aDXiM7',
      'signatureMethod' => 'HMAC-SHA1'
  );

  $token = new Zend_Oauth_Token();
  $token->set("access_token", $accessToken);

  $config->setOptions($options);
  $config->setToken($token);
  $config->setRequestMethod('GET');
  $url = 'https://mail.google.com/mail/b/' .
       $emailAddress .
       '/imap/';

  $httpUtility = new Zend_Oauth_Http_Utility();

  /**
   * Get an unsorted array of oauth params,
   * including the signature based off those params.
   */
  $params = $httpUtility->assembleParams(
      $url,
      $config);

  /**
   * Sort parameters based on their names, as required
   * by OAuth.
   */
  ksort($params);

  /**
   * Construct a comma-deliminated,ordered,quoted list of
   * OAuth params as required by XOAUTH.
   *
   * Example: oauth_param1="foo",oauth_param2="bar"
   */
  $first = true;
  $oauthParams = '';
  foreach ($params as $key => $value) {
    // only include standard oauth params
    if (strpos($key, 'oauth_') === 0) {
      if (!$first) {
        $oauthParams .= ',';
      }
      $oauthParams .= $key . '="' . urlencode($value) . '"';
      $first = false;
    }
  }

  /**
   * Generate SASL client request, using base64 encoded
   * OAuth params
   */
  $initClientRequest = 'GET ' . $url . ' ' . $oauthParams;
  $initClientRequestEncoded = base64_encode($initClientRequest);

  return $initClientRequestEncoded;
}
