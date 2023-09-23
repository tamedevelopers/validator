<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Tame;


class CsrfToken{

    /**
     * session name
     * 
     * @var string
     */
    static private $session;

    /**
     * csrf token
     * 
     * @var string
     */
    static private $token;
    
    /**
     * Use Csrf Token 
     * @var bool
     */
    static private $allow_csrf = true;


    /**
     * initialize data
     * 
     * @return void
     */
    static private function init()
    {
        self::$session  = 'csrf_token';
        self::$token    = bin2hex(random_bytes(32));

        // if defined
        if(defined('GLOBAL_FORM_CSRF_TOKEN')){
            self::$allow_csrf = GLOBAL_FORM_CSRF_TOKEN;
        }

        // if csrf is allowed to be use
        if(self::$allow_csrf){
            // Start the session if it has not already been started
            if (session_status() == PHP_SESSION_NONE) {
                @session_start();
            }

            // Generate on new page load
            self::generateTokenOnPageLoad();
        }
    }

    /**
     * Get Csrf Token
     * 
     * @return string|null
     */
    static public function getToken()
    {
        self::init();

        // session
        $session = isset($_SESSION[self::$session]) 
                    ? json_decode($_SESSION[self::$session], false)
                    : null;
        
        // if session data is available
        if($session) {
            $token = Tame::decryptStr($session->encryption, $session->key, $session->passphrase);
        } else {
            $token = self::generateToken();
        }

        return $token;
    }

    /**
     * Validate Token
     * 
     * @return bool
     */
    static public function validateToken(?string $token) 
    {
        self::init();

        // if csrf is allowed to be use
        if(self::$allow_csrf){
            if (!isset($_SESSION[self::$session])) {
                return false;
            }

            // check if same value ion both data
            return hash_equals(self::getToken(), $token);
        }

        return false;
    }

    /**
     * Generate Public input Csrf Token
     * 
     * @return string|null
     */
    static public function generateCSRFInputToken() 
    {
        self::init();

        $session    = self::$session;
        $token      = self::getToken();

        if(self::$allow_csrf){
            echo '<input type="hidden" name="'.$session.'" value="'.$token.'">';
        }
    }

    /**
     * Generate Csrf Token
     * 
     * @return string|null
     */
    static private function generateToken() 
    {
        self::init();

        // if csrf is allowed to be use
        if(self::$allow_csrf){
            $_SESSION[self::$session] = Tame::encryptStr(self::$token);
            return self::$token;
        }
    }

    /**
     * Generate Csrf Token on Page Load
     * 
     * @return void
     */
    static private function generateTokenOnPageLoad()
    {
        // if csrf is allowed to be use
        if(self::$allow_csrf){
            if ($_SERVER['REQUEST_METHOD'] == 'GET' && empty($_GET)) {
                unset($_SESSION[self::$session]);
                $_SESSION[self::$session] = Tame::encryptStr(self::$token);
            } 
        }
    }

}