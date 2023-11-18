<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Traits;

use Tamedevelopers\Validator\Methods\Datatype;
use Tamedevelopers\Validator\Methods\CsrfToken;
use Tamedevelopers\Validator\Methods\RuleIndicator;
use Tamedevelopers\Validator\Methods\ValidatorMethod;
use Tamedevelopers\Validator\Methods\ExceptionMessage;

trait ValidateSuccessTrait {

        
    /**
     * csrf_token
     *
     * @var string
     */
    private $csrf_token = '_token';

    
    /**
     * Validate Rules
     *
     * @return $this
     */
    private function validateRules()
    {
        return $this->submitInitialization($this->rules);
    }
    
    /**
     * Create Form Validation Data
     * 
     * @param  array $data
     * 
     * @return $this
     */
    private function submitInitialization(?array $data = [])
    {
        
        // only begin validation when submitted
        if(ValidatorMethod::isSubmitted())
        {
            // set to false
            $this->proceed = $this->flashVerify = false;
            
            /**
            * Set Message Error Type
            */
            $this->setMessageErrorType($this->config['errorType']);

            /**
            * Start csrf_token session
            */
            new CsrfToken($this->config['csrf']);

            /**
             * Check for Csrf Token before allow processing of form data
             * 
             * If Csrf Token is allowed to be used, then we Check if found along with form
             * Or If token not correct for encrypted token in the session
             */
            if($this->config['csrf']){
                // set error to true
                $this->setErrorTrue();

                if(!$this->param->has($this->csrf_token)){
                    $this->message  = ExceptionMessage::csrfTokenNotFound();
                    return $this;
                } elseif(!CsrfToken::validateToken($this->param->{$this->csrf_token})){
                    $this->message  = ExceptionMessage::csrfTokenMismatch();
                    return $this;
                }
            }
            
            // start loop process
            foreach($data as $key => $message)
            {
                
                // create data types
                $ruleValidator = RuleIndicator::validate($key);

                /**
                * Configuration error
                */
                if($ruleValidator === "indicator")
                {
                    $this->setErrorTrue();
                    $this->message  = ExceptionMessage::indicator();
                    break;
                }

                // Check response error from input flags
                $checkDataType = Datatype::validate($ruleValidator);

                // allowed errors handling type
                // if error is to be handled one by one
                if($this->config['errorType'] === false)
                {
                    // set error to true
                    $this->setErrorTrue();
                    
                    if($this->isDataTypeNotSet($checkDataType)){
                        $this->message  = $message;
                        break;
                    }
                    elseif($this->isDataTypeNotFound($checkDataType)){
                        // ExceptionMessage::notFound($ruleValidator)
                        $this->message  = $message;
                        break;
                    } else{

                        // set to false
                        $this->setErrorFalse(); 

                        //operator function checker
                        $this->config['operator']     = $this->operatorMethod($ruleValidator);

                        if($this->isOperatorError()){
                            $this->message = ExceptionMessage::comparison($ruleValidator);
                            $this->setErrorTrue();
                            break;
                        }
                        else{
                            if(!is_null($this->config['operator']) && $this->config['operator']){
                                $this->message  = $message;
                                $this->setErrorTrue();
                                break;
                            }
                        }
                    }
                }
                
                // if errors is to be handled as an array
                // multiple error handling 
                else {
                    // set error to true
                    $this->setErrorTrue();
                    
                    $input_name = $ruleValidator['input_name'];
                    
                    if($this->isDataTypeNotSet($checkDataType)){ 
                        if(!in_array($input_name, array_keys($this->message))){
                            $this->message[$input_name]    = $message;
                        }
                    }
                    elseif($this->isDataTypeNotFound($checkDataType)){
                        if(!in_array($input_name, array_keys($this->message))){
                            // ExceptionMessage::notFound($ruleValidator);
                            $this->message[$input_name]    = $message;
                        }
                    } else{
                        //operator function checker
                        $this->config['operator'] = $this->operatorMethod($ruleValidator);

                        // check error types
                        if($this->isOperatorError()){
                            $this->message[$input_name]    = ExceptionMessage::comparison($ruleValidator);
                            break;
                        }
                        elseif(!is_null($this->config['operator']) && $this->config['operator']){
                            if(!in_array($input_name, array_keys($this->message))){
                                $this->message[$input_name]    = $message;
                            }
                        }
                        else{
                            // if no message error exist 
                            // meaning array count is 0
                            if(!count($this->message)){
                                $this->setErrorFalse();
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
                $this->proceed = $this->flashVerify = true;
            } 
            // error message on empty rules
            elseif(count($this->rules) === 0){
                $this->message  = ExceptionMessage::emptyRules();
            }
            
            return $this;
        }
        
        return $this;
    }
    
    /**
     * errOperator
     *
     * @return bool
     */
    private function isOperatorError()
    {
        if($this->config['operator'] === 'error'){
            return true;
        }

        return false;
    }
    
    /**
     * isDataTypeNotSet
     *
     * @param  mixed $type
     * @return bool
     */
    private function isDataTypeNotSet($type)
    {
        if(($type === false || $type === '!isset')){
            return true;
        } 

        return false;
    }
    
    /**
     * isDataTypeNotFound
     *
     * @param  mixed $type
     * @return bool
     */
    private function isDataTypeNotFound($type)
    {
        if(($type === false || $type === '!found')){
            return true;
        } 

        return false;
    }

    /**
     * Set error to true
     * 
     * @return void
     */
    private function setErrorTrue()
    {
        $this->error = true;
    }

    /**
     * Set error to false
     * 
     * @return void
     */
    private function setErrorFalse()
    {
        $this->error = false;
    }

    /**
     * Only ignore if validate metho, has been manually called
     *
     * @return mixed
     */
    private function ignoreIfValidatorHasBeenCalled()
    {
        if(!$this->isValidatedCalled){
            $this->validate();
        }
    }
    
}
