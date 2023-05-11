<?php

declare(strict_types=1);

namespace UltimateValidator;

trait SubmitSuccessTrait {
  
    
     /**
     * Create Form Validation Data
     * 
     * @param  array $data
     * 
     * @return object\data\submitInitialization
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
                
                // allowed errors handling type
                // if error is to be handled one by one
                if($this->errorType === false){
                    // set error to true
                    $this->isErrorTrue();

                    if($checkDataType === false || $checkDataType === '!isset'){
                        $this->message  = $message;
                        break;
                    }
                    elseif($checkDataType === false || $checkDataType === '!found'){
                        $this->message  = ExceptionMessage::notFound($dataType);
                        break;
                    } else{
                        // set to false
                        $this->isErrorFalse(); 

                        //operator function checker
                        $this->operator     = $this->operatorMethod($dataType);

                        if($this->operator === 'error'){
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
                else{
                    // set error to true
                    $this->isErrorTrue();

                    if($checkDataType === false || $checkDataType === '!isset'){
                        if(!in_array($dataType['variable'], array_keys($this->message))){
                            $this->message[$dataType['variable']]    = $message;
                        }
                    }
                    elseif($checkDataType === false || $checkDataType === '!found'){
                        if(!in_array($dataType['variable'], array_keys($this->message))){
                            $this->message[$dataType['variable']]    = ExceptionMessage::notFound($dataType);
                        }
                    } else{
                        //operator function checker
                        $this->operator = $this->operatorMethod($dataType);

                        // check error types
                        if($this->operator === 'error'){
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