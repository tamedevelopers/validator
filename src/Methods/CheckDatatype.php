<?php

declare(strict_types=1);

namespace UltimateValidator;

class CheckDatatype {
  
    /**
     * Private instance of parent validator
     * 
     * @var object\object
     */
    private static $object;


    /**
     * Check if form data is set 
     * And if data type is correct
     * 
     * @param  array $data_flags\[data_type|variable|operator|value]
     * 
     * !isset if param is not set
     * false if expected data type is not correct
     * true|data on success
     * 
     * @return array|string|bool\check
     */
    public static function check(?array $data_flags)
    {
        // parent object
        self::$object = UltimateMethods::$object;

        // if input parameter is isset -- proceed to error validating
        $type = true;

        // check for ENUM types
        if(self::checkEmun($data_flags)){
            $type = self::validateForminput($data_flags);
        }

        else{
            // check if value param isset
            if(UltimateMethods::checkIfParamIsset($data_flags['variable'])){
                $type = self::validateForminput($data_flags);
            } 
            
            // if data passes is not in form elements
            elseif(!in_array($data_flags['variable'], array_keys(self::$object->param))){
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
     * @param  array $data_flags\[data_type|variable|operator|value]
     * 
     * @return bool\checkEmun
     */
    protected static function checkEmun(?array $data_flags)
    {
        if(in_array(strtolower($data_flags['data_type']), ['enum', 'en'])){
            return true;
        }

        return false;
    }

    /**
     * Validate form input
     * If either of those input type is not checked yet, input not send along form
     * So we need determine if it should be set by the system, or not.
     * 
     * @param  array $data_flags\[data_type|variable|operator|value]
     * 
     * @return array|string|bool\validateForminput
     */
    protected static function validateForminput(?array $data_flags)
    {
        // lowercase
        $data_flags['data_type'] = strtolower($data_flags['data_type']);

        switch($data_flags['data_type']){

            // email validation
            case (in_array($data_flags['data_type'], ['email', 'e'])):
                $type = filter_input(self::$object->type, $data_flags['variable'], FILTER_VALIDATE_EMAIL);
                break;
                
            // integer validation
            case (in_array($data_flags['data_type'], ['int', 'i'])):
                $type = filter_input(self::$object->type, $data_flags['variable'], FILTER_VALIDATE_INT);
                break;
                
            // float validation
            case (in_array($data_flags['data_type'], ['float', 'f'])):
                $type = filter_input(self::$object->type, $data_flags['variable'], FILTER_VALIDATE_FLOAT);
                break;
              
            // url validation
            case (in_array($data_flags['data_type'], ['url', 'u'])):
                $type = filter_input(self::$object->type, $data_flags['variable'], FILTER_VALIDATE_URL);
                break;
                
            // array validation
            case (in_array($data_flags['data_type'], ['array', 'a'])):
                if(is_string(self::$object->param[$data_flags['variable']])){
                    $array = json_decode(self::$object->param[$data_flags['variable']], true);
                }else{
                    $array = self::$object->param[$data_flags['variable']];
                }
                $type = isset(self::$object->param[$data_flags['variable']]) && is_array($array) && count($array) > 0 ? true : false;
                break;
               
            // bool|bool validation
            case (in_array($data_flags['data_type'], ['bool', 'b'])):
                $type = filter_input(self::$object->type, $data_flags['variable'], FILTER_VALIDATE_BOOL);
                break;

            // enum validation
            case (in_array($data_flags['data_type'], ['enum', 'en'])):
                // if value is not set -- it will return null
                if(is_null(filter_input(self::$object->type, $data_flags['variable']))){
                    $type = '';
                }else{
                    $type = filter_input(self::$object->type, $data_flags['variable']);
                }

                // mostly for value of 0
                if(empty($type) && $type != '0') {
                    $type = false;
                }
                break;

            // string validation
            default:
                $type = self::filter_string(filter_input(self::$object->type, $data_flags['variable']));
                // mostly for value of 0
                if(empty($type) && $type != '0') {
                    $type = false;
                }
                break;
        }

        return $type;
    }

    /**
     * Filter sanitize string
     *
     * @param string $string
     * 
     * @return string
    */
    private static function filter_string(?string $string = null)
    {
        // htmlspecialchars('data', ENT_HTML5);
        return htmlspecialchars((string) $string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
}