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
        // max separator
        $maxReplacements = 4;

        // Split the string by the separator (: or |)
        $parts = preg_split('/[:|]/', $string, $maxReplacements);

        // error of indicator
        if(empty($parts[0]) || empty($parts[1])){
            return "indicator";
        }

        $parts['data_type']  = $parts[0];
        $parts['input_name'] = $parts[1];

        //create operator
        if(isset($parts[2])){
            $parts['operator'] = $parts[2];
        }
        if(isset($parts[3])){
            $parts['value'] = $parts[3];
        }

        // unset un-used keys `Key string values from array` 
        unset($parts[0]);
        unset($parts[1]);
        unset($parts[2]);
        unset($parts[3]);

        return $parts;
    }
    
}