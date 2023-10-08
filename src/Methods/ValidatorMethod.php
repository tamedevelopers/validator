<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Str;
use Tamedevelopers\Validator\Validator;
use Tamedevelopers\Support\Collections\Collection;

class ValidatorMethod {

    /**
     * Private instance of parent validator
     * @var mixed
     */
    public static $validator;

    /**
     * Initialize methods to have access to global validator (self::$validator)
     *
     * @return \Tamedevelopers\Validator\Validator
     */
    public static function initialize($validator)
    {
        self::$validator = $validator;

        return self::$validator;
    }

    /**
     * Return value of needed parameters form objects
     *
     * @param  string|null $param  form input name.
     * @return bool
     */
    public static function checkIfParamIsset($param = null)
    {
        if(self::$validator->config['request'] == INPUT_POST){
            return isset($_POST[$param]);
        } elseif(self::$validator->config['request'] == INPUT_GET){
            return isset($_GET[$param]);
        }
        
        return isset($_REQUEST[$param]);
    }

    /**
     * Set default params on load 
     * @param string|int $request
     * - Default type is INPUT_GET as value 1
     * 
     * @return mixed
     */
    public static function setParams($request = 1)
    {
        // if the param is not set then we use request method to 
        // determine data received data from forms

        // get Data using POST method
        if($request === INPUT_POST){
            self::$validator->param = $_POST;
        } elseif($request === INPUT_GET){
            self::$validator->param = $_GET;
        } else{
            self::$validator->param = $_REQUEST;
        }

        // convert into a collection of data
        self::$validator->param  = new Collection(self::$validator->param);
        
        return self::$validator;
    }

    /**
     * Check if Form has been submitted
     * @return bool
     */
    public static function isSubmitted()
    {
        $items = self::$validator->param;
        if($items && $items->count() > 0){
            return true;
        }

        return false;
    }

    /**
     * Allow data performaceon `GET` Request before submit
     * @return bool
     */
    public static function isGetRequestBeforeSubmitted()
    {
        if(Str::lower($_SERVER['REQUEST_METHOD']) === 'get'){
            // allow when get TYPE if not submitted
            if(!self::isSubmitted()){
                return true;
            }
        }

        return false;
    }

    /**
     * Needed input values from submited form object
     * @param  array|null  $keys of input
     * 
     * @return array
     */
    public static function only($keys = null)
    {
        $data = [];
        if(is_array($keys)){
            foreach($keys as $key){
                if(in_array($key, array_keys(self::$validator->param->toArray()))){
                    $data[$key] = self::$validator->param[$key];
                }
            }

            return $data;
        }
    }

    /**
     * Remove input values from submited form object
     * @param  array|null  $keys of input
     * 
     * @return array
     */
    public static function except($keys = null)
    {
        $data = self::$validator->param->toArray();
        if(is_array($keys)){
            foreach($keys as $key){
                if(in_array($key, array_keys($data))){
                    unset($data[$key]);
                }
            }

            return $data;
        }
    }

    /**
     * Check if param is set in parent param
     *
     * @param string|null $key
     *
     * @return bool
     */
    public static function has($key = null)
    {
        if(in_array($key, array_keys(self::$validator->param->toArray()))){
            return true;
        }

        return false;
    }

     /**
     * Remove value of parameters form objects
     *
     * @param array|null $keys
     * @param array|null $data
     *
     * @return array
     */
    public static function merge($keys = null, $data = null)
    {
        if(is_array($keys) && is_array($data)){
            return array_merge($keys,  $data);
        }
    }

    /**
     * Get needed data from array 
     * @param  array  $keys of needed data
     * @param  array|null  $allData param to check from
     * 
     * @return array
     */
    public static function onlyData($keys = null, $allData = null)
    {
        $data = [];
        if(is_array($keys) && is_array($data)){
            foreach($keys as $key){
                if(in_array($key, array_keys($allData))){
                    $data[$key] = $allData[$key];
                }
            }

            return $data;
        }
    }

    /**
     * Get all needed params except the removed onces
     * @param  array|null  $keys of to remove from parameters
     * @param  array|null  $data param to check from
     * 
     * @return mixed
     */
    public static function exceptData($keys = null, $data = null)
    {
        if(is_array($keys) && is_array($data)){
            foreach($keys as $key){
                if(in_array($key, array_keys($data))){
                    unset($data[$key]);
                }
            }

            return $data;
        }
    }

    /**
     * Return previously entered value
     * 
     * @param string $key of param name
     * 
     * @param mixed $default
     * [optional] 
     * 
     * @return mixed
     */
    public static function old($key = null, $default = null)
    {
        // in array keys
        $formData = self::getForm();

        if(is_array($formData) && in_array($key, array_keys($formData))){
            // get data using key
            $data = $formData[$key] ?? $default;

            // check if the data to be returned is an array
            if(is_array($data)){
                return array_combine($data, $data);
            }
            
            return $data;
        }

        return $default;
    }

    /**
     * Resolve flash message and save in memory
     * @param \Tamedevelopers\Validator\Validator|mixed $validator
     * 
     * @return mixed
     */
    public static function resolveFlash(Validator $validator)
    {
        // if message is an array
        if(is_array($validator->message)){
            foreach($validator->message as $message){ 
                $validator->flash['message'][] = $message; 
            }
        } else{
            $validator->flash['message'][] = $validator->message;
        }

        // configure error class
        self::configErrorClass($validator);

        // if form validation is successful
        if($validator->flashVerify){
            $validator->flash['class'] = $validator->class['success'];
        } else{
            // Set class to error
            $validator->flash['class'] = $validator->class['error'];
        }
        
        return self::$validator;
    }

    /**
     * Reset flash data to default values
     * @param \Tamedevelopers\Validator\Validator|mixed $validator
     * 
     * @return mixed
     */
    public static function resetFlash(Validator $validator)
    {
        $validator->flash = [
            'message'   => [],
            'class'     => '',
        ];
        
        return self::$validator;
    }

    /**
     * Return error message in the form of converted string
     * 
     * @return string
     */
    public static function getMessage()
    {
        return implode(' <br>', self::$validator->flash['message']);
    }

    /**
     * Return error class
     * 
     * @return string
     */
    public static function getClass()
    {
        return self::$validator->flash['class'];
    }

    /**
     * Get Form Data
     * Return form data if isset
     * 
     * @param string $type |attribute|attributes
     * .ie form (array) of param | attributes (object) of param 
     * 
     * @return mixed 
     */
    public static function getForm()
    {
        $mainForm = self::$validator->param->toArray();
        return !empty($mainForm) 
                ? $mainForm
                : self::$validator->all()->param->toArray();
    }

    /**
     * configErrorClass
     *
     * @param  mixed $validator
     * @return mixed
     */
    static private function configErrorClass(&$validator)
    {
        if(defined('TAME_VALIDATOR_CONFIG')){
            $validator->class = TAME_VALIDATOR_CONFIG['class'];
        }

        return $validator->class;
    }

}