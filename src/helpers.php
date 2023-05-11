<?php 

use UltimateValidator\RequestMethod;
use UltimateValidator\UltimateValidator;


if (! function_exists('opForm')) {

    /**
     * Get Form Instance \ PHP Form Validator
     * @param mixed $attribute 
     * - Any outside parameter you would want to use within the form instance
     * 
     * @return object\data\UltimateValidator
     */
    function opForm(mixed $attribute = null)
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