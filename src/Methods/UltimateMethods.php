<?php

declare(strict_types=1);

/*
 * This file is part of ultimate-validator.
 *
 * (c) Tame Developers Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UltimateValidator;

/**
 * UltimateMethods
 *
 * @package   Ultimate\Validator
 * @author    Tame Developers <tamedevelopers@gmail.co>
 * @copyright 2021-2023 Tame Developers
 */
class UltimateMethods {
  
    /**
     * Private instance of parent validator
     * 
     * @var object\object
     */
    public static $object;

    /**
     * Initialize methods to have access to global object (self::$object)
     *
     * @param  object $object Ultimate validator onject.
     *
     * @return void\initialize
     */
    public static function initialize( UltimateValidator $object )
    {
        self::$object = $object;

        return self::$object;
    }

    /**
     * Return value of needed parameters form objects
     *
     * @param  string $param  form input name.
     *
     * @return boolean\checkIfParamIsset true|false
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
     * Check if there's a param passed to __contructor or not
     * 
     * @param array|object\param $param 
     * form param or constructed params data 
     * 
     * @return object\setParams
     */
    public static function setParams($param)
    {
        // if the param is not set then we use request method to 
        // determine data received data from forms
        if(is_null($param)){
            // POST | GET | COOKIE
            self::$object->param    = $_REQUEST;
            self::$object->params   = (object) self::$object->param;
        }else{
            self::$object->param    = filter_input_array(self::$object->type);
            self::$object->params   = (object) self::$object->param;
        }
        return self::$object;
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
                if(in_array($key, array_keys(self::$object->param))){
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
        $data = self::$object->param;
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
     * @param string\key $key
     *
     * @return boolean
     */
    public static function has(?string $key = null)
    {
        if(in_array($key, array_keys(self::$object->param))){
            return true;
        }
        return false;
    }

     /**
     * Remove value of parameters form objects
     *
     * @param array\keys $keys
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
     * @param  array\keys  $keys of needed data
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
     * @param  array|keys  $keys of to remove from parameters
     * @param  array|data  $data param to check from
     * 
     * @return array 
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
     * @param  string|array|object  $message 
     * @return string|json\echoJson  
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
     * @return array|object|string\old
     */
    public static function old($key = null)
    {
        // in array keys
        $formData = self::getForm()['attribute'];
        if(is_array($formData) && in_array($key, array_keys($formData))){
            return $formData[$key];
        }
    }

    /**
     * Get Form Data
     * Return form data if isset
     * 
     * @param string $type |attribute|attributes
     * .ie form (array) of param | attributes (object) of param 
     * 
     * @return array|object 
     */
    public static function getForm($type = null)
    {
        // create form structure
        $data = [
            'attribute'     => self::$object->param,
            'attributes'    => (object) self::$object->param
        ];

        // in array keys
        if(in_array($type, array_keys($data))){
            return $data[$type];
        }
        return $data;
    }


}