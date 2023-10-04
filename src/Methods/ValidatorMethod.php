<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Validator\Validator;
use Tamedevelopers\Support\Collections\Collection;

class ValidatorMethod {

    /**
     * Private instance of parent validator
     * 
     * @var \Tamedevelopers\Validator\Validator
     */
    public static $object;

    /**
     * Initialize methods to have access to global object (self::$object)
     *
     * @param \Tamedevelopers\Validator\Validator|mixed $object
     *
     * @return void
     */
    public static function initialize(Validator $object)
    {
        self::$object = $object;

        return self::$object;
    }

    /**
     * Return value of needed parameters form objects
     *
     * @param  string $param  form input name.
     *
     * @return bool
     */
    public static function checkIfParamIsset(?string $param = null)
    {
        if(self::$object->type == INPUT_POST){
            return isset($_POST[$param]);
        }
        return isset($_GET[$param]);
    }

    /**
     * Set default params on load 
     * @param $type
     * - Default type is INPUT_GET as value 1
     * 
     * @return object
     */
    public static function setParams(?int $type = 1)
    {
        // if the param is not set then we use request method to 
        // determine data received data from forms

        // get Data using POST method
        if($type === INPUT_POST){
            self::$object->param = $_POST;
        } elseif($type === INPUT_GET){
            self::$object->param = $_GET;
        } else{
            self::$object->param = filter_input_array(self::$object->type);
        }

        // convert into a collection of data
        self::$object->param  = new Collection(self::$object->param);
        
        return self::$object;
    }

    /**
     * Check if Form has been submitted
     * @return bool
     */
    public static function isSubmitted()
    {
        $items = self::$object->param;
        if($items && $items->count() > 0){
            return true;
        }
        return false;
    }

    /**
     * Check server request method if `GET`
     * ->beforeSubmit Method will apply when REQUEST is GET
     * 
     * @return bool
     */
    public static function isRequestMethod()
    {
        $type = trim(strtolower((string) $_SERVER['REQUEST_METHOD'])) ?? '';
        if($type === 'get'){
            // allow when get TYPE is not submitted
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
                if(in_array($key, array_keys(self::$object->param->toArray()))){
                    $data[$key] = self::$object->param[$key];
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
        $data = self::$object->param->toArray();
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
     * @param string $key
     *
     * @return bool
     */
    public static function has(?string $key = null)
    {
        if(in_array($key, array_keys(self::$object->param->toArray()))){
            return true;
        }
        return false;
    }

     /**
     * Remove value of parameters form objects
     *
     * @param array $keys
     *
     * @return array
     */
    public static function merge(?array $keys = null, ?array $data = null)
    {
        if(is_array($keys) && is_array($data)){
            return array_merge($keys,  $data);
        }
    }

    /**
     * Get needed data from array 
     * @param  array  $keys of needed data
     * @param  array  $allData param to check from
     * 
     * @return array
     */
    public static function onlyData(?array $keys = null, ?array $allData = null)
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
     * @param  array  $keys of to remove from parameters
     * @param  array  $data param to check from
     * 
     * @return mixed
     */
    public static function exceptData(?array $keys = null, ?array $data = null)
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
     * Returns encoded JSON object of response and message
     * 
     * @param  int|float  $response
     * @param  mixed  $message 
     * @return string
     */
    public static function echoJson(?int $response = 0, $message = null)
    {
        echo json_encode(['response' => $response, 'message' => $message]);
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
     * @param \Tamedevelopers\Validator\Validator|mixed $object
     * 
     * @return mixed
     */
    public static function resolveFlash(Validator $object)
    {
        // if message is an array
        if(self::isArray($object->message)){
            foreach($object->message as $message){ 
                $object->flash['message'][] = $message; 
            }
        } else{
            $object->flash['message'][] = $object->message;
        }

        // configure error class
        self::configErrorClass($object);

        // set default class error
        $object->flash['class'] = $object->error_class['error'];

        // if form validation is successful
        if($object->flashVerify){
            $object->flash['class'] = $object->error_class['success'];
        }
        
        return self::$object;
    }

    /**
     * Reset flash data to default values
     * @param \Tamedevelopers\Validator\Validator|mixed $object
     * 
     * @return mixed
     */
    public static function resetFlash(Validator $object)
    {
        $object->flash = [
            'message'   => [],
            'class'     => '',
        ];
        
        return self::$object;
    }

    /**
     * Return error message in the form of converted string
     * 
     * @return string
     */
    public static function getMessage()
    {
        return implode(' <br>', self::$object->flash['message']);
    }

    /**
     * Return error class
     * 
     * @return string
     */
    public static function getClass()
    {
        return self::$object->flash['class'];
    }

    /**
     * Get Form Data
     * Return form data if isset
     * 
     * @param string $type |attribute|attributes
     * .ie form (array) of param | attributes (object) of param 
     * 
     * @return object 
     */
    public static function getForm()
    {
        $mainForm = self::$object->param->toArray();
        return !empty($mainForm) 
                ? $mainForm
                : self::$object->all()->param->toArray();
    }

    /**
     * configErrorClass
     *
     * @param  mixed $object
     * @return void
     */
    static private function configErrorClass(&$object)
    {
        if(defined('GLOBAL_FORM_CLASS')){
            $object->error_class = GLOBAL_FORM_CLASS;
        }

        return $object->error_class;
    }

    /**
     * Check if a string is a valid JSON format.
     *
     * @param string $string The string to check
     * @return bool True if the string is a valid JSON, false otherwise
     */
    private static function isJson(?string $string = null)
    {
        // Try decoding the string
        $decoded = json_decode($string);

        // Check if there are no JSON syntax errors and the result is not null
        return (json_last_error() === JSON_ERROR_NONE) && ($decoded !== null);
    }

    /**
     * Check if a data is a valid Array format.
     *
     * @param object|array|string $data The data to check
     * @return bool True if the data is a valid array, false otherwise
     */
    private static function isArray($data = null)
    {
        // Check if data is an array or not
        return is_array($data) ? true : false;
    }

}