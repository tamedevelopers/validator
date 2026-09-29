<?php 

use Tamedevelopers\Support\Str;
use Tamedevelopers\Validator\Validator;
use Tamedevelopers\Validator\Methods\CsrfToken;
use Tamedevelopers\Validator\Methods\ValidatorMethod;


/**
 * Helps without calling the method multiple times
 */
$Tame_isAppFramework = function_exists('Tame_isAppFramework') ? Tame_isAppFramework() : false;


if (! function_exists('form')) {
    
    /**
     * Get Form Instance - PHP Form Validator
     * 
     * @param  mixed $attribute     Additional Data usable within the form
     * @return \Tamedevelopers\Validator\Validator
     */
    function form($attribute = null)
    {
        return new Validator($attribute);
    }
}

if (! $Tame_isAppFramework && ! function_exists('old')) {
    
    /**
     * Return previously entered value
     * 
     * @param string|null $key Input name
     * @param mixed $default
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
     * @param 'post'|'get'|'all'|null $request
     * @param  array{error: string, success: string} $class
     * @return void
     */
    function config_form($error_type = false, $csrf_token = true, $request = null, $class = [])
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

if (! $Tame_isAppFramework && ! function_exists('csrf_token')) {
    
    /**
     * Get Csrf Token
     */
    function csrf_token(): string
    {
        return (new CsrfToken)->getToken();
    }
}

if (! $Tame_isAppFramework && ! function_exists('csrf')) {

    /**
     * Generate Input for Csrf Token
     */
    function csrf(): string|null
    {
        return (new CsrfToken)->generateCSRFInputToken();
    }
}