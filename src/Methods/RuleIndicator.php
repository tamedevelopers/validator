<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

class RuleIndicator {
  
    /**
     * Each form submit strings
     * [string:name:>:15 => 'First name is required']
     * 
     * @param string $string 
     * - string like :string::name .
     * 
     * @return array 
     * - [data_type|input_name|operator|value]
     */
    public static function validate($string)
    {
        // clean string
        $string =  str_replace([':', '|'], ':', $string);

        // explode data
        $data = explode(":", $string);

        // count how many occurence in string
        $find_occur = substr_count($string, ':'); 

        // error
        if(count($data) > 4 || $find_occur > 3 || !isset($data[1])){
            return "indicator";
        }

        $data['data_type']  = $data[0];
        $data['input_name'] = $data[1];

        //create operator
        if(isset($data[2])){
            $data['operator'] = $data[2];
        }
        if(isset($data[3])){
            $data['value'] = $data[3];
        }

        // unset un-used keys `Key string values from array` 
        unset($data[0]);
        unset($data[1]);
        unset($data[2]);
        unset($data[3]);

        return $data;
    }
    
}