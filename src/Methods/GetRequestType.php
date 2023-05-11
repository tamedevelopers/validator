<?php

declare(strict_types=1);

namespace UltimateValidator;

class GetRequestType {
  
    /**
     * The value of request type.
     * 
     * @return int  
     */
    public static function request()
    {
        // get form request type
        $type = strtoupper((string) $_SERVER['REQUEST_METHOD']);

        switch(strtoupper($type)){
            case 'GET':
                $request = INPUT_GET;
                break;
                
            default:
                $request = INPUT_POST;
                break;
        }

        return $request;
    }
    
}