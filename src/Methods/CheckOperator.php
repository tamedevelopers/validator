<?php

declare(strict_types=1);

namespace UltimateValidator;

class CheckOperator {
  
    /**
     * Checking for flag type error
     * Returns true on error found and false is no error is found
     *
     * @param  \UltimateValidator\UltimateValidator|mixed $object
     * @param  mixed $dataType
     * @return bool
     */
    public static function check(UltimateValidator $object, $dataType = [])
    {
        $object->operator   = false;
        $flagOperator       = $dataType['operator'];
        $flagValue          = $dataType['value'];

        if(UltimateMethods::checkIfParamIsset($dataType['variable'])){

            //equal to operator
            if($flagOperator == '==')
            {
                $dataString = $object->param[$dataType['variable']];
                if($dataString == $flagValue){
                    $object->operator = true;
                }
            }

            //strictly equal to operator
            elseif($flagOperator == '===')
            {
                $dataString = $object->param[$dataType['variable']];
                if($dataString === $flagValue){
                    $object->operator = true;
                }
            }

            //not equal to operator
            elseif($flagOperator == '!=')
            {
                $dataString = $object->param[$dataType['variable']];
                if($dataString != $flagValue){
                    $object->operator = true;
                }
            }

            //strictly not equal to operator
            elseif($flagOperator == '!==')
            {
                $dataString = $object->param[$dataType['variable']];
                if($dataString !== $flagValue){ 
                    $object->operator = true;
                }
            }

            //greater than operator
            elseif($flagOperator == '>')
            {
                $dataString = $object->param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString > (float) $flagValue){
                        $object->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString > (float) $flagValue){
                        $object->operator = true;
                    }
                }
            }

            //greater than or equal to operator
            elseif($flagOperator == '>=')
            {
                $dataString = $object->param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString >= (float) $flagValue){
                        $object->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString >= (float) $flagValue){
                        $object->operator = true;
                    }
                }
            }

            //less than operator
            elseif($flagOperator == '<')
            {
                $dataString = $object->param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $flagValue){
                        $object->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $flagValue){
                        $object->operator = true;
                    }
                }
            }

            //less than or equal to operator
            elseif($flagOperator == '<=')
            {
                $dataString = $object->param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  <= (float) $flagValue){
                        $object->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  <= (float) $flagValue){
                        $object->operator = true;
                    }
                }
            }

            //less than or greather than to operator
            elseif($flagOperator == '<||>')
            {
                $dataString = $object->param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $flagValue || $dataString  > (float) $flagValue){
                        $object->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $flagValue || $dataString  > (float) $flagValue){
                        $object->operator = true;
                    }
                }
            }

            //less than and greather than to operator
            elseif($flagOperator == '<&&>')
            {
                $dataString = $object->param[$dataType['variable']];
                // if str_len | sl
                if(in_array($dataType['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $flagValue && $dataString  > (float) $flagValue){
                        $object->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $flagValue && $dataString  > (float) $flagValue){
                        $object->operator = true;
                    }
                }
            }
            
        }

        return $object->operator;
    }
    
}