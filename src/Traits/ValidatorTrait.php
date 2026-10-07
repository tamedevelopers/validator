<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Traits;

use Closure;
use Tamedevelopers\Support\Tame;
use Tamedevelopers\Support\Server;
use Tamedevelopers\Validator\Methods\Operator;
use Tamedevelopers\Validator\Methods\Constant;
use Tamedevelopers\Support\Collections\Collection;
use Tamedevelopers\Validator\Methods\GetRequestType;
use Tamedevelopers\Validator\Methods\ValidatorMethod;

trait ValidatorTrait {

    /**
     * @var ValidatorMethod Static Validator Instance 
     */
    private static $validtorStaticMethod;

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
     * Calling validator method
     */
    private static function vaMtd(): ValidatorMethod
    {
        if(self::$validtorStaticMethod){
            return self::$validtorStaticMethod;
        }

        self::$validtorStaticMethod = new ValidatorMethod();

        return self::$validtorStaticMethod;
    }

    /**
     * Get needed data from array 
     * 
     * @param  array|null  $keys of needed data
     * @param  array|null  $data param to check from
     * @return array
     */
    public function onlyData($keys = null, $data = null)
    {
        $method = self::vaMtd();

        $keys = $method->isCollectionInstance($keys) ? $keys?->toArray() : $keys;
        $data = $method->isCollectionInstance($data) ? $data?->toArray() : $data;

        return $method->onlyData($keys, $data);
    }

    /**
     * Get all needed params except the removed onces
     * 
     * @param  array|null  $keys of data to remove from parameters
     * @param  array|null  $data param to check from
     * @return array 
     */
    public function exceptData($keys = null, $data = null)
    {
        $method = self::vaMtd();

        $keys = $method->isCollectionInstance($keys) ? $keys?->toArray() : $keys;
        $data = $method->isCollectionInstance($data) ? $data?->toArray() : $data;

        return $method->exceptData($keys, $data);
    } 

    /**
     * Get Form Data
     * 
     * @param string|null $key
     * @return Collection
     */
    public function getForm($key = null)
    {
        return self::vaMtd()->getForm($key);
    }

    /**
     * Get param data directly
     * 
     * @param string|null $key
     * @return mixed
     */
    public static function param($key = null)
    {
        return self::vaMtd()->param($key);
    }

    /**
     * Return a new response from the application.
     *
     * @param  mixed  $content
     * @param  int    $status
     * @param  array  $headers
     * @return mixed
     */
    public static function json($content = [], int $status = 200, array $headers = [])
    {
        $response = Tame::json($content, $status, $headers);

        if(Tame::isAppFramework()){
            return $response;
        }

        // If not in a framework and JsonResponse
        // we send response automatically
        if (self::vaMtd()->isJsonResponse($response)) {
            return $response->send();
        }

        return $response;
    }

    /**
     * Alias for `echoJson` method
     * 
     * @param  int $response
     * @param  mixed  $message 
     * @return mixed
     */
    public static function jsonEcho(int $response = 0, $message = null)
    {
        return self::echoJson($response, $message);
    }

    /**
     * Returns encoded JSON object of response and message
     * 
     * @param  int $response
     * @param  mixed  $message 
     * @return mixed
     */
    public static function echoJson(int $response = 0, $message = null)
    {
        return Tame::jsonEcho($response, $message);
    }

    /**
     * Return error message in the form of converted string
     * @return string
     */
    public function getMessage()
    {
        return self::vaMtd()->getMessage();
    }

    /**
     * Return error class
     * 
     * @return string
     */
    public function getClass()
    {
        return self::vaMtd()->getClass();
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
        $this->config['request'] = Constant::POST;

        $this->initalizeAfterRequestSet();

        // set params
        $param = self::vaMtd()->getAndSetSourceParam(Constant::POST);
        $this->param = $param->param;

        return $this;
    }

    /**
     * Convert Form Request to GET
     */
    public function get(): self
    {
        $this->config['request'] = Constant::GET;

        $this->initalizeAfterRequestSet();

        // set params
        $param = self::vaMtd()->getAndSetSourceParam(Constant::GET);
        $this->param = $param->param;

        return $this;
    }

    /**
     * Convert Form Request to REQUEST_METHOD
     * @return $this
     */
    public function all()
    {
        $this->config['request'] = Constant::REQUEST;
        
        $this->initalizeAfterRequestSet();

        // set params
        $param = self::vaMtd()->getAndSetSourceParam(Constant::REQUEST);
        $this->param = $param->param;

        return $this;
    }

    /**
     * Convert Form Request to REQUEST_METHOD
     * @return $this
     */
    public function any()
    {
        return $this->all();
    }

    /**
     * Convert data to array
     * 
     * @param mixed $data
     * @return array
     */ 
    public function toArray($data = null)
    {
        return Server::toArray($data);
    }
    
    /**
     * Convert data to object
     * 
     * @param mixed $data
     * @return object
     */ 
    public function toObject($data = null)
    {
        return Server::toObject($data);
    }
    
    /**
     * Convert data to json
     * 
     * @param mixed $data
     * @return string
     */ 
    public function toJson($data = null)
    {
        return Server::toJson($data);
    }

    /**
     * Initalize Method Instance After Request has been set
     */
    private function initalizeAfterRequestSet(): void
    {
        self::vaMtd()->initialize($this);
    }

    /**
     * Operator Method
     * 
     * @param  array|null $dataType  array.
     * @return bool 
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
     * 
     * @param string|null $request
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
     * 
     * @param  bool $errorType.
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
