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
 * Validator
 *
 * @package   Ultimate\Validator
 * @author    Tame Developers <tamedevelopers@gmail.co>
 * @author    Fredrick Peterson <fredi.peterson2000@gmail.com>
 * @copyright 2021-2023 Tame Developers
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class UltimateValidator implements UltimateInterface
{

    /**
    * param
    */ 
    public  $param; 

    /**
    * params object replica of $param
    */ 
    public  $params; 

    /**
    * attribute 
    * @var string|array|object
    */
    public $attribute;

    /**
    * public message notice
    * @var string|array|object
    */
    public  $message; 

    /**
    * data type
    * @var boolean|null
    */
    public  $type; 

    /**
    * operator check
    */
    public  $operator; 

    /**
    * flash
    * @var boolean
    */
    public $flash;

    /**
    * proceed
    * @var boolean
    */
    private $proceed;

    /**
    * error 
    * @var boolean
    */
    private $error    = false;


    /**
    * @param  array    $param form array like $_POST or $_GET.
    * @param  string   $type string like POST or GET - case-insensitive
    * @param  string|int|array|object|resource   $attribute any outside param you would want to use within the form
    * @return object   returns an object for chaining
    */
    public function __construct(?array $param = null, ?string $type = null, $attribute = null) 
    {
        $this->message      = [];
        $this->type         = $this->getType($type);
        $this->attribute    = $attribute;
        
        // initialize methods
        UltimateMethods::initialize($this);
        
        // set params
        UltimateMethods::setParams($param);

        return $this;
    }

    /**
    * @param  array     $data Constant like INPUT_XXX.
    * @param  boolean   $allowedType --  if to display all error once or one after another
    * @return object|response class object return.
    */
    public function submit(array $data, ?bool $allowedType = false) 
    {
        //only begin validation when submitted
        if(isset($this->param) && count($this->param) > 0)
        {
            //set to false
            $this->proceed = false;

            /**
            * after isset function call
            */
            $this->afterSubmit($this);

            /**
            * if allowed error type is true
            * Error type converted to arrays
            */
            if($allowedType){
                $this->message  = [];
            }else{
                $this->message  = "";
            }


            // start loop process
            foreach($data as $key => $message){


                // create data types
                $dataType = CreateDatatype::create($key);

                /**
                * Configuration error
                */
                if($dataType === "indicator"){
                    $this->error    = true;
                    $this->message  = ExceptionMessage::indicator();
                    break;
                }

                //check response error from input flags
                $checkDataType = CheckDatatype::check($dataType);

                // allowed error handling type
                if($allowedType == false){
                    if($checkDataType === false || $checkDataType === '!isset'){
                        $this->message  = $message;
                        $this->error    = true;
                        break;
                    }
                    elseif($checkDataType === false || $checkDataType === '!found'){
                        $this->message  = ExceptionMessage::notFound($dataType);
                        $this->error    = true;
                        break;
                    } else{
                        $this->error = false; // set to false

                        //operator function checker
                        $this->operator     = $this->operatorMethod($dataType);

                        if($this->operator === 'error'){
                            $this->error      = true;
                            $this->message    = ExceptionMessage::comparison($dataType);
                            break;
                        }
                        else{
                            if(!is_null($this->operator) && $this->operator){
                                $this->message  = $message;
                                $this->error    = true;
                                break;
                            }
                        }
                    }
                }
                else{
                    if($checkDataType === false || $checkDataType === '!isset'){
                        if(!in_array($dataType['variable'], array_keys($this->message))){
                            $this->message[$dataType['variable']]    = $message;
                        }
                        $this->error        = true;
                    }
                    elseif($checkDataType === false || $checkDataType === '!found'){
                        if(!in_array($dataType['variable'], array_keys($this->message))){
                            $this->message[$dataType['variable']]    = ExceptionMessage::notFound($dataType);
                        }
                        $this->error    = true;
                    } else{
                        //operator function checker
                        $this->operator = $this->operatorMethod($dataType);

                        if($this->operator === 'error'){
                            $this->error        = true;
                            $this->message[$dataType['variable']]    = ExceptionMessage::comparison($dataType);
                            break;
                        }

                        elseif(!is_null($this->operator) && $this->operator){
                            if(!in_array($dataType['variable'], array_keys($this->message))){
                                $this->message[$dataType['variable']]    = $message;
                            }
                            $this->error        = true;
                        }
                        else{
                            // if no message error exist 
                            // meaning array count is 0
                            if(!count($this->message)){
                                $this->error = false; // set to false
                            }
                        }
                    }
                }
            }

            

            /**
            * if validation succeeds without error | 
            * set proceed value to | true
            */
            if(!$this->error){
                $this->proceed = true;
            }
            return $this;
        }
        
        /**
        * before isset function call
        */
        else{
            $this->beforeSubmit($this);
        }
        return $this;
    }

    /**
     * @param  callable  $function.
     * @param  null|void  pass a $var into the funtion to access the returned object
     * usage   ->error(  function($response){}  );
     * @return object|response class object on error.
     */
    public function error($function)
    {
        if(!is_null($this->proceed)  && $this->proceed === false){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }
    
    /**
     * @param  callable  $function.
     * @param  null|void  pass a $var into the funtion to access the returned object
     * usage   ->success(  function($response){}  );
     * @return object|response class object on success.
     */
    public function success($function)
    {
        if(!is_null($this->proceed)  && $this->proceed){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }
    
    /**
     * @param  callable  $function.
     * Before form is set
     * usage   ->beforeSubmit(  function($response){}  );
     * @return void\Response class object on success
     */
    public function beforeSubmit($function)
    {
        if(!isset($this->param)){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }

    /**
     * @param  callable  $function.
     * After form is set
     * usage   ->afterSubmit(  function($response){}  );
     * @return void\Response class object on success
     */
    public function afterSubmit($function)
    {
        if(isset($this->param) && count($this->param) > 0){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }
    
    /**
     * Needed input values from submited form object
     * @param  array|null  $keys of input
     * 
     * @return array
     */
    public function only($keys = null)
    {
        return UltimateMethods::only($keys);
    }

    /**
     * Remove input values from submited form object
     * @param  array|null  $keys of input
     * 
     * @return array
     */
    public function except($keys = null)
    {
        return UltimateMethods::except($keys);
    }

    /**
     * Check if param is set in parent param
     *
     * @param string\key $key
     *
     * @return boolean
     */
    public function has(?string $key = null)
    {
        return UltimateMethods::has($key);
    }

    /**
     * Remove value of parameters form objects
     *
     * @param array\keys $keys
     *
     * @return array
     */
    public function merge(?array $keys = null, ?array $data = null)
    {
        return UltimateMethods::merge($keys, $data);
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
     * Return previously entered value
     * 
     * @param string $key of param name
     * 
     * @return array|object|string\old
     */
    public function old($key = null)
    {
        return UltimateMethods::old($key);
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
     * @param  array $dataType  array.
     * @return boolean or false.
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
    * @param  string $type    string like POST or GET.
    * @return int            The value of request type.
    */
    private function getType($type = null)
    {
        $this->type = GetRequestType::request($type);

        return $this->type;
    }


}
