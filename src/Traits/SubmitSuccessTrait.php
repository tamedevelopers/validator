<?php

declare(strict_types=1);

namespace UltimateValidator;

use UltimateValidator\CsrfToken;

trait SubmitSuccessTrait {
  
    
     /**
     * Create Form Validation Data
     * 
     * @param  array $data
     * 
     * @return $this
     */
    private function submitInitialization(?array $data = [])
    {
        /**
        * before isset function call
        */
        $this->beforeSubmit($this);
        
        // only begin validation when submitted
        if(UltimateMethods::isSubmitted())
        {
            // set to false
            $this->proceed = $this->flashVerify = false;
            
            /**
            * after isset function call
            */
            $this->afterSubmit($this);
            
            /**
            * Convert message type
            */
            $this->allowedType($this->errorType);

            /**
             * Check for Csrf Token before allow processing of form data
             * 
             * If Csrf Token is allowed to be used, then we Check if found along with form
             * Or If token not correct for encrypted token in the session
             */
            if($this->allow_csrf){
                // set error to true
                $this->isErrorTrue();

                if(!$this->param->has('csrf_token')){
                    $this->message  = ExceptionMessage::csrfTokenNotFound();
                    return $this;
                } elseif(!CsrfToken::validateToken($this->param->csrf_token)){
                    $this->message  = ExceptionMessage::csrfTokenMismatch();
                    return $this;
                }
            }
            
            // start loop process
            foreach($data as $key => $message){
                
                // create data types
                $dataType = CreateDatatype::create($key);
                
                /**
                * Configuration error
                */
                if($dataType === "indicator"){
                    $this->isErrorTrue();
                    $this->message  = ExceptionMessage::indicator();
                    break;
                }

                //check response error from input flags
                $checkDataType = CheckDatatype::check($dataType);
                
                // allowed errors handling type
                // if error is to be handled one by one
                if($this->errorType === false){

                    // set error to true
                    $this->isErrorTrue();
                    
                    if($this->isDataTypeNotSet($checkDataType)){
                        $this->message  = $message;
                        break;
                    }
                    elseif($this->isDataTypeNotFound($checkDataType)){
                        // ExceptionMessage::notFound($dataType)
                        $this->message  = $message;
                        break;
                    } else{

                        // set to false
                        $this->isErrorFalse(); 

                        //operator function checker
                        $this->operator     = $this->operatorMethod($dataType);

                        if($this->isOperatorError()){
                            $this->message = ExceptionMessage::comparison($dataType);
                            $this->isErrorTrue();
                            break;
                        }
                        else{
                            if(!is_null($this->operator) && $this->operator){
                                $this->message  = $message;
                                $this->isErrorTrue();
                                break;
                            }
                        }
                    }
                }
                
                // if errors is to be handled as an array
                // multiple error handling 
                else{
                    // set error to true
                    $this->isErrorTrue();
                    
                    if($this->isDataTypeNotSet($checkDataType)){ 
                        if(!in_array($dataType['variable'], array_keys($this->message))){
                            $this->message[$dataType['variable']]    = $message;
                        }
                    }
                    elseif($this->isDataTypeNotFound($checkDataType)){
                        if(!in_array($dataType['variable'], array_keys($this->message))){
                            // ExceptionMessage::notFound($dataType);
                            $this->message[$dataType['variable']]    = $message;
                        }
                    } else{
                        //operator function checker
                        $this->operator = $this->operatorMethod($dataType);

                        // check error types
                        if($this->isOperatorError()){
                            $this->message[$dataType['variable']]    = ExceptionMessage::comparison($dataType);
                            break;
                        }
                        elseif(!is_null($this->operator) && $this->operator){
                            if(!in_array($dataType['variable'], array_keys($this->message))){
                                $this->message[$dataType['variable']]    = $message;
                            }
                        }
                        else{
                            // if no message error exist 
                            // meaning array count is 0
                            if(!count($this->message)){
                                $this->isErrorFalse();
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
        if($this->operator === 'error'){
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
        if($type === false || $type === '!isset'){
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
        if($type === false || $type === '!found'){
            return true;
        } 

        return false;
    }

    /**
     * Set error to true
     * 
     * @return void
     */
    private function isErrorTrue()
    {
        $this->error = true;
    }

    /**
     * Set error to false
     * 
     * @return void
     */
    private function isErrorFalse()
    {
        $this->error = false;
    }
    
}
