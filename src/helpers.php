<?php 

use Tamedevelopers\Support\Str;
use Tamedevelopers\Validator\Validator;
use Tamedevelopers\Validator\Methods\CsrfToken;
use Tamedevelopers\Validator\Methods\ValidatorMethod;


if (! function_exists('form')) {

    /**
     * Get Form Instance \ PHP Form Validator
     * @param mixed $attribute 
     * - Any outside parameter you would want to use within the form instance
     * 
     * @return \Tamedevelopers\Validator\Validator
     */
    function form($attribute = null)
    {
        return new Validator($attribute);
    }
}

if (! AppIsNotCorePHP() && ! function_exists('old')) {
    
    /**
     * Return previously entered value
     * 
     * @param string $key of param name
     * @param mixed $default
     * [optional] 
     * 
     * @return mixed
     */
    function old($key = null, $default = null)
    {
       return ValidatorMethod::old($key, $default);
    }
}

if (! function_exists('config_form')) {
    
    /**
     * Set Global Form Configuration
     *
     * @param  bool $error_type
     * @param  bool $csrf_token
     * 
     * @param  string|null $request
     * - [post|get|all]
     * 
     * @param  array $class
     * - [error|success]
     * 
     * @return void
     */
    function config_form(?bool $error_type = false, ?bool $csrf_token = true, $request = null, ?array $class = [])
    {
        // config holder
        if(!defined('TAME_VALIDATOR_CONFIG')){

            // If request not in array
            $request = Str::lower($request);
            $request = match ($request) {
                'post', 'get', 'all' => $request,
                default => 'post'
            };

            // configure class
            $class = array_merge([
                'error'     => 'alert alert-danger',
                'success'   => 'alert alert-success'
            ], $class);

            // create constant
            define('TAME_VALIDATOR_CONFIG', [
                'error_type'    => $error_type,
                'csrf_token'    => $csrf_token,
                'request'       => $request,
                'class'         => $class,
            ]);
        }
    }
}

if (! AppIsNotCorePHP() && ! function_exists('csrf_token')) {

    /**
     * Get Csrf Token
     * 
     * @return string
     */
    function csrf_token()
    {
        return (new CsrfToken)->getToken();
    }
}

if (! AppIsNotCorePHP() && ! function_exists('csrf')) {

    /**
     * Generate Input for Csrf Token
     * 
     * @return string
     */
    function csrf()
    {
        return (new CsrfToken)->generateCSRFInputToken();
    }
}