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
 * CheckOperator
 *
 * @package   Ultimate\Validator
 * @author    Tame Developers <tamedevelopers@gmail.co>
 * @copyright 2021-2023 Tame Developers
 */
class CheckOperator {
  
    
    /**
     * Checking for flag type error
     * Returns true on error found and false is no error is found
     * 
     * @param  array\flag $flag Keypairs.
     * @param  object\object $object of parent class ($this).
     * 
     * @return boolean\ false|true
    */
    public static function check($dataType = [], UltimateValidator $object)
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