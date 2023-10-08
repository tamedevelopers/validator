<?php 

use Tamedevelopers\Support\Str;
use Tamedevelopers\Validator\Validator;
use Tamedevelopers\Validator\Methods\CsrfToken;


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

if (! function_exists('old')) {
    
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
       return form()->old($key, $default);
    }
}

if (! function_exists('config_form')) {
    
    /**
     * Set Global Form Configuration
     *
     * @param  bool $error_type
     * @param  bool $csrf_token
     * @param  string|null $request
     * @param  array $class
     * @return void
     */
    function config_form(?bool $error_type = false, ?bool $csrf_token = true, $request = null, array $class = [])
    {
        // If request not in array
        if(!in_array(Str::lower($request), ['post', 'get', 'any', 'all'])){
            $request = 'any';
        }

        // configure class
        $class = array_merge([
            'error'     => 'alert alert-danger',
            'success'   => 'alert alert-success'
        ], $class);

        // config holder
        if(!defined('TAME_VALIDATOR_CONFIG')){
            define('TAME_VALIDATOR_CONFIG', [
                'error_type' => $error_type,
                'csrf_token' => $csrf_token,
                'request'   => $request,
                'class'     => $class,
            ]);
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