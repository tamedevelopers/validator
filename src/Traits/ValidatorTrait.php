<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Traits;

use Closure;
use Tamedevelopers\Support\Tame;
use Tamedevelopers\Support\Server;
use Tamedevelopers\Validator\Methods\Operator;
use Tamedevelopers\Validator\Methods\GetRequestType;
use Tamedevelopers\Validator\Methods\ValidatorMethod;

trait ValidatorTrait {

    /**
     * Run a callback 
     *
     * @param  Closure|null $closure
     * @return mixed
     */
    private function callback($closure = null)
    {
        if(Tame::isClosure($closure)){
           return $closure($this);
        }
    }
  
    /**
     * Use form methods without actually submitting form
     * 
     * @param  Closure $closure
     * @return mixed
     */
    public function noInterface($closure)
    {
        return $this->callback($closure);
    }

    /**
     * Get needed data from array 
     * @param  array|null  $keys of needed data
     * @param  array|null  $allData param to check from
     * 
     * @return array
     */
    public function onlyData($keys = null, $data = null)
    {
        $keys = ValidatorMethod::isCollectionInstance($keys) ? $keys?->toArray() : $keys;
        $data = ValidatorMethod::isCollectionInstance($data) ? $data?->toArray() : $data;

        return ValidatorMethod::onlyData($keys, $data);
    }

    /**
     * Get all needed params except the removed onces
     * @param  array|null  $keys of data to remove from parameters
     * @param  array|null  $data param to check from
     * 
     * @return array 
     */
    public function exceptData($keys = null, $data = null)
    {
        $keys = ValidatorMethod::isCollectionInstance($keys) ? $keys?->toArray() : $keys;
        $data = ValidatorMethod::isCollectionInstance($data) ? $data?->toArray() : $data;

        return ValidatorMethod::exceptData($keys, $data);
    } 

    /**
     * Get Form Data
     * 
     * @param string $type
     * 
     * @return array|object 
     * - Return form data if isset
     */
    public function getForm($type = null)
    {
        return ValidatorMethod::getForm($type);
    }

    /**
     * Return a JSON response with status code and message
     *
     * @param  int   $response
     * @param  mixed $message
     * @param  int   $statusCode
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public static function json(int $response = 0, $message = null, int $statusCode = 200)
    {
        return Tame::json($response, $message, $statusCode);
    }

    /**
     * Alias for `echoJson` method
     * 
     * @param  int $response
     * @param  mixed  $message 
     * @return mixed<json>
     */
    public static function jsonEcho(int $response = 0, $message = null)
    {
        self::echoJson($response, $message);
    }

    /**
     * Returns encoded JSON object of response and message
     * 
     * @param  int $response
     * @param  mixed  $message 
     * @return mixed<json>  
     */
    public static function echoJson(int $response = 0, $message = null)
    {
        Tame::jsonEcho($response, $message);
    }

    /**
     * Return error message in the form of converted string
     * @return string
     */
    public function getMessage()
    {
        return ValidatorMethod::getMessage();
    }

    /**
     * Return error class
     * 
     * @return string
     */
    public function getClass()
    {
        return ValidatorMethod::getClass();
    }

    /**
     * Alias form `errorType` method
     * @param  bool $type
     * @return $this
     */
    public function error(?bool $type = false)
    {
        return $this->errorType($type);
    }

    /**
     * Error type handler
     * @param  bool $type
     * @return $this
     */
    public function errorType(?bool $type = false)
    {
        $this->config['errorType'] = $type;

        return $this;
    }

    /**
     * CSRF Token
     * @param  bool $type\ Token type
     * - true|false \Default is false
     * 
     * @return $this
     */
    public function token(?bool $type = false)
    {
        $this->config['csrf'] = $type;

        return $this;
    }

    /**
     * Convert Form Request to POST
     * 
     * @return $this
     */
    public function post()
    {
        $this->config['request'] = INPUT_POST;

        // initialize methods
        ValidatorMethod::initialize($this);

        // set params
        ValidatorMethod::setParams($this->config['request']);

        return $this;
    }

    /**
     * Convert Form Request to GET
     * 
     * @return $this
     */
    public function get()
    {
        $this->config['request'] = INPUT_GET;

        // initialize methods
        ValidatorMethod::initialize($this);

        // set params
        ValidatorMethod::setParams($this->config['request']);

        return $this;
    }

    /**
     * Convert Form Request to REQUEST_METHOD
     * 
     * @return $this
     */
    public function all()
    {
        $this->config['request'] = 2;

        // initialize methods
        ValidatorMethod::initialize($this);

        // set params
        ValidatorMethod::setParams($this->config['request']);

        return $this;
    }

    /**
     * Convert Form Request to REQUEST_METHOD
     * 
     * @return $this
     */
    public function any()
    {
        return $this->all();
    }

    /**
     * Convert data to array
     * @param mixed $data
     * 
     * @return array
     */ 
    public function toArray($data = null)
    {
        return Server::toArray($data);
    }
    
    /**
     * Convert data to object
     * @param mixed $data
     * 
     * @return object
     */ 
    public function toObject($data = null)
    {
        return Server::toObject($data);
    }
    
    /**
     * Convert data to json
     * @param mixed $data
     * 
     * @return string
     */ 
    public function toJson($data = null)
    {
        return Server::toJson($data);
    }

    /**
     * @param  array|null $dataType  array.
     * @return bool 
     * - [true or false].
     */
    private function operatorMethod($dataType = null)
    {
        $this->config['operator'] = null;

        //comparison operator command
        if(isset($dataType['operator']) && !empty($dataType['operator'])){
            $this->config['operator'] = 'error';
            //value check command
            if(isset($dataType['value'])){
                $this->config['operator'] = Operator::validate($this, $dataType);
            }
        }
        return $this->config['operator'];
    }

    /**
     * Get Form Request
     * @param string|null $request
     * 
     * @return int
     */
    private function getFormRequest($request = null)
    {
        // if defined
        if(defined('TAME_VALIDATOR_CONFIG')){
            if(empty($request)){
                $request = TAME_VALIDATOR_CONFIG['request'];
            }
        }

        return GetRequestType::request($request);
    }

    /**
     * Convert message error type
     * @param  bool $errorType.
     * 
     * @return $this
     */
    private function setMessageErrorType(?bool $errorType = false)
    {
        /**
        * if allowed error type is true
        * Error message type converted to arrays
        */
        if($errorType){
            $this->message  = [];
        } else{
            $this->message  = "";
        }

        return $this;
    }

}
