<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;


class Operator {
  
    /**
     * Checking for flag type error
     * Returns true on error found and false is no error is found
     *
     * @param  array $param
     * @param  array $dataType
     * @return bool
     */
    public static function validate($param, $dataType = [])
    {
        $operator   = false;
        $flagOperator       = $dataType['operator'];
        $flagValue          = $dataType['value'];

        if(ValidatorMethod::checkIfParamIsset($dataType['variable'])){

            //equal to operator
            if($flagOperator == '==')
            {
                $dataString = $param[$dataType['variable']];
                if($dataString == $flagValue){
                    self::setOperator($operator);
                }
            }

            //strictly equal to operator
            elseif($flagOperator == '===')
            {
                $dataString = $param[$dataType['variable']];
                if($dataString === $flagValue){
                    self::setOperator($operator);
                }
            }

            //not equal to operator
            elseif($flagOperator == '!=')
            {
                $dataString = $param[$dataType['variable']];
                if($dataString != $flagValue){
                    self::setOperator($operator);
                }
            }

            //strictly not equal to operator
            elseif($flagOperator == '!==')
            {
                $dataString = $param[$dataType['variable']];
                if($dataString !== $flagValue){ 
                    self::setOperator($operator);
                }
            }

            //greater than operator
            elseif($flagOperator == '>')
            {
                $dataString = $param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString > (float) $flagValue){
                        self::setOperator($operator);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString > (float) $flagValue){
                        self::setOperator($operator);
                    }
                }
            }

            //greater than or equal to operator
            elseif($flagOperator == '>=')
            {
                $dataString = $param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString >= (float) $flagValue){
                        self::setOperator($operator);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString >= (float) $flagValue){
                        self::setOperator($operator);
                    }
                }
            }

            //less than operator
            elseif($flagOperator == '<')
            {
                $dataString = $param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $flagValue){
                        self::setOperator($operator);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $flagValue){
                        self::setOperator($operator);
                    }
                }
            }

            //less than or equal to operator
            elseif($flagOperator == '<=')
            {
                $dataString = $param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  <= (float) $flagValue){
                        self::setOperator($operator);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  <= (float) $flagValue){
                        self::setOperator($operator);
                    }
                }
            }

            //less than or greather than to operator
            elseif($flagOperator == '<||>')
            {
                $dataString = $param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $flagValue || $dataString  > (float) $flagValue){
                        self::setOperator($operator);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $flagValue || $dataString  > (float) $flagValue){
                        self::setOperator($operator);
                    }
                }
            }

            //less than and greather than to operator
            elseif($flagOperator == '<&&>')
            {
                $dataString = $param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $flagValue && $dataString  > (float) $flagValue){
                        self::setOperator($operator);
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $flagValue && $dataString  > (float) $flagValue){
                        self::setOperator($operator);
                    }
                }
            }
            
        }

        return $operator;
    }
    
    /**
     * setOperator
     *
     * @param  mixed $object
     * @return void
     */
    static private function setOperator(&$operator)
    {
        $operator = true;
    }
    
}