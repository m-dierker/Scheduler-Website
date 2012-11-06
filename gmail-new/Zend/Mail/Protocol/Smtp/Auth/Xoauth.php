<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Login.php 20096 2010-01-06 02:05:09Z bkarwin $
 */


/**
 * @see Zend_Mail_Protocol_Smtp
 */
require_once 'Zend/Mail/Protocol/Smtp.php';


/**
 * Performs LOGIN authentication
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Protocol_Smtp_Auth_Xoauth extends Zend_Mail_Protocol_Smtp
{
    /**
     * LOGIN username
     *
     * @var string
     */
    protected $_username;


    /**
     * LOGIN password
     *
     * @var string
     */
    protected $_password;


    /**
     * Constructor.
     *
     * @param  string $host   (Default: 127.0.0.1)
     * @param  int    $port   (Default: null)
     * @param  array  $config Auth-specific parameters
     * @return void
     */
    public function __construct($host = '127.0.0.1', $port = null, $config = null)
    {
        if (is_array($config)) {
            if (isset($config['xoauth_request'])) {
                $this->_xoauth_request = $config['xoauth_request'];
            }
        }

        parent::__construct($host, $port, $config);
    }


    /**
     * Perform LOGIN authentication with supplied credentials
     *
     * @return void
     */
    public function auth()
    {
        // Ensure AUTH has not already been initiated.
        parent::auth();
        $cmd = 'AUTH XOAUTH ' . $this->_xoauth_request;
        echo("Sending auth token\n -- $cmd \n");
        $this->_send($cmd);
        $this->_expect(235);
        $this->_auth = true;
    }
}
