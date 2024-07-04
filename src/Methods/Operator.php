<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;


class Operator {
  
    /**
     * Checking for flag type error
     * Returns true on error found and false is no error is found
     *
     * @param  mixed $validator
     * @param  array $dataType
     * @return bool
     */
    public static function validate($validator, $dataType = [])
    {
        $operatorError  = false;
        $operator       = $dataType['operator'];
        $value          = $dataType['value'];
        $input_name     = $dataType['input_name'];
        $param          = $validator->param;

        if(ValidatorMethod::checkIfParamIsset($input_name)){

            //equal to operator
            if($operator == '==')
            {
                $dataString = $param[$input_name];
                if($dataString == $value){
                    self::setOperator($operatorError);
                }
            }

            //strictly equal to operator
            elseif($operator == '===')
            {
                $dataString = $param[$input_name];
                if($dataString === $value){
                    self::setOperator($operatorError);
                }
            }

            //not equal to operator
            elseif($operator == '!=')
            {
                $dataString = $param[$input_name];
                if($dataString != $value){
                    self::setOperator($operatorError);
                }
            }

            //strictly not equal to operator
            elseif($operator == '!==')
            {
                $dataString = $param[$input_name];
                if($dataString !== $value){ 
                    self::setOperator($operatorError);
                }
            }

            //greater than operator
            elseif($operator == '>')
            {
                $dataString = $param[$input_name];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString > (float) $value){
                        self::setOperator($operatorError);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString > (float) $value){
                        self::setOperator($operatorError);
                    }
                }
            }

            //greater than or equal to operator
            elseif($operator == '>=')
            {
                $dataString = $param[$input_name];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString >= (float) $value){
                        self::setOperator($operatorError);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString >= (float) $value){
                        self::setOperator($operatorError);
                    }
                }
            }

            //less than operator
            elseif($operator == '<')
            {
                $dataString = $param[$input_name];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $value){
                        self::setOperator($operatorError);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $value){
                        self::setOperator($operatorError);
                    }
                }
            }

            //less than or equal to operator
            elseif($operator == '<=')
            {
                $dataString = $param[$input_name];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  <= (float) $value){
                        self::setOperator($operatorError);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  <= (float) $value){
                        self::setOperator($operatorError);
                    }
                }
            }

            //less than or greather than to operator
            elseif($operator == '<or>')
            {
                $dataString = $param[$input_name];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (int) $value || $dataString  > (int) $value){
                        self::setOperator($operatorError);
                    }
                }else{
                    $dataString = (int) $dataString;
                    if($dataString  < (int) $value || $dataString  > (int) $value){
                        self::setOperator($operatorError);
                    }
                }
            }

            //less than and greather than to operator
            elseif($operator == '<and>')
            {
                $dataString = $param[$input_name];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (int) $value && $dataString  > (int) $value){
                        self::setOperator($operatorError);
                    }
                }else{
                    $dataString = (int) $dataString;
                    if($dataString  < (int) $value && $dataString  > (int) $value){
                        self::setOperator($operatorError);
                    }
                }
            }
            
        }

        return $operatorError;
    }
    
    /**
     * setOperator
     *
     * @param  mixed $operatorError
     * @return void
     */
    static private function setOperator(&$operatorError)
    {
        $operatorError = true;
    }
    
}