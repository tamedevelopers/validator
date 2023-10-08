<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Traits;

use Tamedevelopers\Support\Tame;
use Tamedevelopers\Support\Server;
use Tamedevelopers\Validator\Methods\Operator;
use Tamedevelopers\Validator\Methods\GetRequestType;
use Tamedevelopers\Validator\Methods\ValidatorMethod;

trait ValidatorTrait {
  
    /**
     * Use form methods without actually submitting form
     * 
     * @param  callable  $function.
     * @return void
     */
    public function noInterface(callable $function)
    {
        if(is_callable($function)){
            $function($this);
        }
    }

    /**
     * Get needed data from array 
     * @param  array|null  $keys of needed data
     * @param  array|null  $allData param to check from
     * 
     * @return array
     */
    public function onlyData($keys = null, $allData = null)
    {
        return ValidatorMethod::onlyData($keys, $allData);
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
     * Returns encoded JSON object of response and message
     * 
     * @param  int|float  $response
     * @param  mixed  $message 
     * @return string
     * - [json]  
     */
    public function echoJson(?int $response = 0, $message = null)
    {
        return Tame::echoJson($response, $message);
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
     * Error type handler
     * @param  bool $type
     * @return $this
     */
    public function et(?bool $type = false)
    {
        $this->config['errorType'] = $type;

        return $this;
    }

    /**
     * Error type handler
     * @param  bool $type
     * @return $this
     */
    public function errorType(?bool $type = false)
    {
        return $this->et($type);
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
     * CSRF Token
     * @param  bool $type\ Token type
     * - true|false \Default is false
     * 
     * @return $this
     */
    public function csrf(?bool $type = false)
    {
        return $this->token($type);
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
        $this->config['request'] = INPUT_SERVER;

        // initialize methods
        ValidatorMethod::initialize($this);

        // set params
        ValidatorMethod::setParams($this->config['request']);

        return $this;
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
            $request = TAME_VALIDATOR_CONFIG['request'];
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
