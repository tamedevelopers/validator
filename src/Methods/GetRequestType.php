<?php

declare(strict_types=1);

namespace UltimateValidator;

class GetRequestType {
  
    /**
     * The value of request type.
     * @param string $type
     * 
     * @return int  
     */
    public static function request(?string $type = null)
    {
        // set default value for request type to POST
        $requestType = INPUT_POST;

        // always empty|null except `Config_opForm` has been used
        if(!empty($type)){
            $type = strtoupper(trim($type));
            if($type == 'ALL'){
                $requestType = $_SERVER['REQUEST_METHOD'] == 'GET' 
                                ? INPUT_GET 
                                : INPUT_POST;
            } elseif(in_array($type, ['GET', 'POST'])){
                $requestType = $type == 'GET' 
                                ? INPUT_GET 
                                : INPUT_POST;
            }
        }

        return $requestType;
    }
    
}