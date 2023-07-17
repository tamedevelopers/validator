<?php 

use UltimateValidator\CsrfToken;
use UltimateValidator\RequestMethod;
use UltimateValidator\UltimateValidator;


if (! function_exists('form')) {

    /**
     * Get Form Instance \ PHP Form Validator
     * @param mixed $attribute 
     * - Any outside parameter you would want to use within the form instance
     * 
     * @return object\data\UltimateValidator
     */
    function form(mixed $attribute = null)
    {
        return new UltimateValidator($attribute);
    }
}


if (! function_exists('request')) {

    /**
     * Get Server Request 
     * @param $key \Optional
     * - $key => Array data key value if accesible or returns entire data 
     * 
     * @return mixed\data\RequestMethod
     */
    function request(?string $key = null)
    {
        return new RequestMethod();
    }
}


if (! function_exists('config_form')) {

    /**
     * Set Global Configuration of FORM Setting
     * @param array $options
     * - [csrf_token => bool] \True|False 
     * - True|False
     * 
     * @return void
     */
    function config_form(?array $option = [])
    {
        $default = [
            'error_type'    => $option['error_type'] ?? false,
            'csrf_token'    => $option['csrf_token'] ?? true,
            'request'       => $option['request'] ?? 'POST',
        ];

        // check if is boolean values
        $default['error_type'] = is_bool($default['error_type']) ? $default['error_type'] : false;
        $default['csrf_token'] = is_bool($default['csrf_token']) ? $default['csrf_token'] : true;

        // Error type
        if(!defined('GLOBAL_OPFORM_ERROR')){
            define('GLOBAL_OPFORM_ERROR', $default['error_type']);
        }

        // Csrf Token
        if(!defined('GLOBAL_OPFORM_CSRF_TOKEN')){
            define('GLOBAL_OPFORM_CSRF_TOKEN', $default['csrf_token']);
        }

        // Request
        if(!defined('GLOBAL_OPFORM_REQUEST')){
            define('GLOBAL_OPFORM_REQUEST', $default['request']);
        }
    }
}


if (! function_exists('csrf_token')) {

    /**
     * Get Csrf Token
     * 
     * @return string
     */
    function csrf_token()
    {
        return CsrfToken::getToken() ;
    }
}


if (! function_exists('csrf')) {

    /**
     * Generate Input for Csrf Token
     * 
     * @return string
     */
    function csrf()
    {
        return CsrfToken::generateCSRFInputToken() ;
    }
}