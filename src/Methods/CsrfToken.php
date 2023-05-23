<?php

declare(strict_types=1);

namespace UltimateValidator;


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
        if(defined('GLOBAL_OPFORM_CSRF_TOKEN')){
            self::$allow_csrf = GLOBAL_OPFORM_CSRF_TOKEN;
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
            $token = CsrfToken::decryptStr($session->encryption, $session->key, $session->passphrase);
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
            $_SESSION[self::$session] = CsrfToken::encryptStr(self::$token);
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
                $_SESSION[self::$session] = CsrfToken::encryptStr(self::$token);
            } 
        }
    }

    /**
     * Decrypt string
     * 
     * @return string
     */
    static private function decryptStr($encryption, $key, $passkey)
    {
        // get encryption
        $openSSL = self::openSSLEncrypt();

        // Store the cipher method
        $ciphering = $openSSL->cipher_algo;

        // Use OpenSSl Encryption method
        $options = $openSSL->options;

        // Store the encryption key
        $key = $key;
        
        // Non-NULL Initialization Vector for encryption
        $passphrase = $passkey;
        
        // Use openssl_decrypt() function to decrypt the data
        return openssl_decrypt($encryption, $ciphering, $key, $options, $passphrase);
    }

    /**
     * Encrypt string
     * 
     * @return string
     */
    static private function encryptStr(?string $string = null)
    {
        // get encryption
        $openSSL = self::openSSLEncrypt();

        // Store the cipher method
        $ciphering = $openSSL->cipher_algo;

        // Store the encryption key
        $key = $openSSL->key;
        
        // Use OpenSSl Encryption method
        $options = $openSSL->options;
        
        // Non-NULL Initialization Vector for encryption
        $passphrase = $openSSL->passphrase;
        
        // Use openssl_encrypt() function to encrypt the data
        $encrypt = openssl_encrypt(
            $string, 
            $ciphering, 
            $key, 
            $options, 
            $passphrase
        );
        
        return json_encode([
            'key'           => $key,
            'passphrase'    => $passphrase,
            'encryption'    => $encrypt
        ]);
    }

    /**
     * Create OPEN SSL Encryption
     * 
     * @return object
     */
    static private function openSSLEncrypt()
    {
        return (object) [
            'key'           => bin2hex(random_bytes(8)),
            'cipher_algo'   => 'BF-CBC',
            'passphrase'    => bin2hex(random_bytes(4)),
            'options'       => OPENSSL_CIPHER_RC2_40
        ];
    }

}