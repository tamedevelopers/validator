<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;


use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Process\Http;

class CsrfToken{

    /**
     * csrf token
     * 
     * @var string
     */
    static private $token;

    /**
     * session name
     * 
     * @var string
     */
    static private $session = '_token';
    
    /**
     * Use Csrf Token 
     * @var bool
     */
    static private $csrf = true;

    /**
     * __construct
     *
     * @param  bool $csrf
     * @return void
     */
    public function __construct($csrf = true)
    {
        self::$csrf     = $csrf;
        self::$token    = bin2hex(random_bytes(32));

        // Start the session if it has not already been started
        if (session_status() == PHP_SESSION_NONE) {
            @session_start();
        }

        // if defined
        if(defined('TAME_VALIDATOR_CONFIG')){
            self::$csrf = TAME_VALIDATOR_CONFIG['csrf_token'];
        }

        // Generate on new page load
        self::generateTokenOnPageLoad();
    }

    /**
     * Get Csrf Token
     * 
     * @return string|null
     */
    static public function getToken()
    {
        $session = $_SESSION[self::$session] ?? null;

        if($session){
            return $session;
        }

        // if csrf is allowed to be use
        if(self::$csrf){
            $_SESSION[self::$session] = self::$token;
            return self::$token;
        }
    }

    /**
     * Validate Token
     * 
     * @return bool
     */
    static public function validateToken(?string $token) 
    {
        // if csrf is allowed to be use
        if(self::$csrf){
            $session = isset($_SESSION[self::$session]) ? $_SESSION[self::$session] : '';

            // check if same value ion both data
            return hash_equals($session, $token);
        }

        return false;
    }

    /**
     * Unset token if available Csrf Token
     * 
     * @return void
     */
    static public function unsetToken()
    {
        if(self::$csrf){
            if(isset($_SESSION[self::$session])){
                unset($_SESSION[self::$session]);
            }
        }
    }

    /**
     * Generate Public input Csrf Token
     * 
     * @return string|null
     */
    static public function generateCSRFInputToken() 
    {
        if(self::$csrf){
            echo '<input type="hidden" name="'.self::$session.'" value="'.self::generateOrIgnore().'">';
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
        if(self::$csrf && Str::upper(Http::method()) == 'GET' && empty($_REQUEST[self::$session])){
            unset($_SESSION[self::$session]);
            $_SESSION[self::$session] = self::generateOrIgnore();
        }
    }

    /**
     * Generate Csrf Token on Page Load
     * 
     * @return void
     */
    static private function generateOrIgnore()
    {
        // generate new key or ignore
        if(empty($_REQUEST[self::$session])){
            return self::getToken();
        }

        return $_SESSION[self::$session] ?? null;
    }

}