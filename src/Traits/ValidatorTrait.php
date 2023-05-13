<?php

declare(strict_types=1);

namespace UltimateValidator;

trait ValidatorTrait {
  
    /**
     * Use form methods without actually submitting form
     * 
     * @param  callable  $function.
     * usage   ->noInterface(  function($response){}  );
     * 
     * @return void\noInterface
     */
    public function noInterface(callable $function)
    {
        if(is_callable($function)){
            $function($this);
        }
    }

    /**
     * Get needed data from array 
     * @param  array\keys  $keys of needed data
     * @param  array  $allData param to check from
     * 
     * @return array
     */
    public function onlyData(?array $keys = null, ?array $allData = null)
    {
        return UltimateMethods::onlyData($keys, $allData);
    }

    /**
     * Get all needed params except the removed onces
     * @param  array|keys  $keys of to remove from parameters
     * @param  array|data  $data param to check from
     * 
     * @return array 
     */
    public function exceptData(?array $keys = null, ?array $data = null)
    {
        return UltimateMethods::exceptData($keys, $data);
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
    public function getForm($type = null)
    {
        return UltimateMethods::getForm($type);
    }

    /**
     * Returns encoded JSON object of response and message
     * 
     * @param  int|float  $response
     * @param  string|array|object  $message 
     * @return string|json\echoJson  
     */
    public function echoJson(?int $response = 0, $message = null)
    {
        return UltimateMethods::echoJson($response, $message);
    }

    /**
     * Return error message in the form of array
     * @param string $key/ message|class
     * 
     * @return string\getErrorMessage
     */
    public function getErrorMessage($key = 'message')
    {
        return UltimateMethods::getErrorMessage($key);
    }

    /**
     * Error type handler
     * @param  bool $type\ Error type
     * 
     * @return object
     */
    public function et(?bool $type = false)
    {
        $this->errorType = $type;

        return $this;
    }

    /**
     * Set Request to POST
     * 
     * @return object
     */
    public function post()
    {
        $this->type = INPUT_POST;

        // initialize methods
        UltimateMethods::initialize($this);

        // set params
        UltimateMethods::setParams($this->type);

        return $this;
    }

    /**
     * Set Request to GET
     * 
     * @return object
     */
    public function get()
    {
        $this->type = INPUT_GET;

        // initialize methods
        UltimateMethods::initialize($this);

        // set params
        UltimateMethods::setParams($this->type);

        return $this;
    }

    /**
     * Set Request to REQUEST_METHOD
     * 
     * @return object
     */
    public function all()
    {
        $this->type = $_SERVER['REQUEST_METHOD'] == 'GET' 
                                ? INPUT_GET 
                                : INPUT_POST;

        // initialize methods
        UltimateMethods::initialize($this);

        // set params
        UltimateMethods::setParams($this->type);

        return $this;
    }

    /**
     * Convert data to array
     * @param mixed $data
     * 
     * @return array
     */ 
    public function toArray(mixed $data = null)
    {
        return json_decode( json_encode($data), true);
    }
    
    /**
     * Convert data to object
     * @param mixed $data
     * 
     * @return object
     */ 
    public function toObject(mixed $data = null)
    {
        return json_decode( json_encode($data), false);
    }
    
    /**
     * Convert data to json
     * @param mixed $data
     * 
     * @return string
     */ 
    public function toJson(mixed $data = null)
    {
        return json_encode($data);
    }

    /**
     * @param  array $dataType  array.
     * @return bool or false.
     */
    private function operatorMethod(?array $dataType = null)
    {
        $this->operator = null;

        //comparison operator command
        if(isset($dataType['operator']) && !empty($dataType['operator'])){
            $this->operator = 'error';
            //value check command
            if(isset($dataType['value'])){
                $this->operator = CheckOperator::check($dataType, $this);
            }
        }
        return $this->operator;
    }

    /**
     * Get Form Request Type
     * @param string $type
     * 
     * @return int
     */
    private function getType(?string $type = null)
    {
        // if defined
        if(defined('GLOBAL_OPFORM_REQUEST')){
            $type = GLOBAL_OPFORM_REQUEST;
        }

        return GetRequestType::request($type);
    }

    /**
     * Convert message error type
     * @param  bool $allowedType.
     * 
     * @return object\allowedType 
     */
    private function allowedType($allowedType)
    {
        /**
        * if allowed error type is true
        * Error message type converted to arrays
        */
        if($allowedType){
            $this->message  = [];
        }else{
            $this->message  = "";
        }

        return $this;
    }

}
