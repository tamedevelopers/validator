<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Tame;

class Datatype {
  
    /**
     * Private instance of parent validator
     * 
     * @var mixed
     */
    private static $validator;


    /**
     * Validate if form data is set 
     * And if data type is correct
     * 
     * @param array $rulesData 
     * - [data_type|input_name|operator|value]
     * 
     * - !isset if param is not set
     * - false if expected data type is not correct
     * - true|data on success
     * 
     * @return mixed
     */
    public static function validate($rulesData)
    {
        // parent object
        self::$validator = ValidatorMethod::$validator;

        // if input parameter is isset -- proceed to error validating
        $type = true;

        // check for ENUM types
        if(self::checkEmun($rulesData)){
            $type = self::validateForminput($rulesData);
        }

        else{

            // check if value param isset
            if(ValidatorMethod::checkIfParamIsset($rulesData['input_name'])){
                $type = self::validateForminput($rulesData);
            } 
            
            // if data passes is not in form elements
            elseif(!in_array($rulesData['input_name'], array_keys(self::$validator->param->toArray()))){
                $type = '!found';
            }

            // else if not isset
            else{
                $type = '!isset';
            }
        }

        return $type;
    }

    /**
     * Majorly for Form's Radio/Checkbox
     * If either of those input type is not checked yet, input not send along form
     * So we need determine if it should be set by the system, or not.
     * 
     * @param  array $rulesData
     * - [data_type|input_name|operator|value]
     * 
     * @return bool
     */
    protected static function checkEmun($rulesData)
    {
        if(in_array(strtolower($rulesData['data_type']), ['enum', 'en'])){
            return true;
        }

        return false;
    }

    /**
     * Validate form input
     * If either of those input type is not checked yet, input not send along form
     * So we need determine if it should be set by the system, or not.
     * 
     * @param  array $rulesData
     * - [data_type|input_name|operator|value]
     * 
     * @return mixed
     */
    protected static function validateForminput($rulesData)
    {
        // lowercase
        $rulesData['data_type'] = Str::lower($rulesData['data_type']);
        $request = self::$validator->config['request'];
        $param = self::$validator->param[$rulesData['input_name']];
        
        switch($rulesData['data_type']){

            // email validation
            case (in_array($rulesData['data_type'], ['email', 'e'])):
                $type = filter_input($request, $rulesData['input_name'], FILTER_VALIDATE_EMAIL);
                break;
                
            // integer validation
            case (in_array($rulesData['data_type'], ['int', 'i'])):
                $type = filter_input($request, $rulesData['input_name'], FILTER_VALIDATE_INT);
                break;
                
            // float validation
            case (in_array($rulesData['data_type'], ['float', 'f'])):
                $type = filter_input($request, $rulesData['input_name'], FILTER_VALIDATE_FLOAT);
                break;
              
            // url validation
            case (in_array($rulesData['data_type'], ['url', 'u'])):
                $type = filter_input($request, $rulesData['input_name'], FILTER_VALIDATE_URL);
                break;
                
            // array validation
            case (in_array($rulesData['data_type'], ['array', 'a'])):
                if(is_string($param)){
                    $array = json_decode($param, true);
                }else{
                    $array = $param;
                }
                $type = isset($param) && is_array($array) && count($array) > 0 ? true : false;
                break;
               
            // bool|bool validation
            case (in_array($rulesData['data_type'], ['bool', 'b'])):
                $type = filter_input($request, $rulesData['input_name'], FILTER_VALIDATE_BOOL);
                break;

            // enum validation
            case (in_array($rulesData['data_type'], ['enum', 'en'])):
                // if value is not set -- it will return null
                if(is_null(filter_input($request, $rulesData['input_name']))){
                    $type = '';
                }else{
                    $type = filter_input($request, $rulesData['input_name']);
                }

                // mostly for value of 0
                if(empty($type) && $type != '0') {
                    $type = false;
                }
                break;

            // string validation
            default:
                $type = Tame::filter_input(
                    filter_input($request, $rulesData['input_name'])
                );

                // mostly for value of 0
                if(empty($type) && $type != '0') {
                    $type = false;
                }
                break;
        }

        return $type;
    }

}