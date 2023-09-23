<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

class CreateDatatype {
  
    /**
     * Each form submit strings
     * [string:first_name:>:15 => 'First name is required']
     * 
     * @param string $key string like :string::name .
     * 
     * @return array 
     * - datas [data_type|variable|operator|value]
     */
    public static function create(?string $key)
    {
        // explode data
        $data = explode(":", $key);

        // explode indicator
        // count how many occurence in string
        $find_occur = substr_count($key, ':'); 

        // error
        if(count($data) > 4 || $find_occur > 3 || !isset($data[1])){
            return "indicator";
        }

        $data['data_type'] = $data[0];
        $data['variable'] = $data[1];

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