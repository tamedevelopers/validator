<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Process\Http;
use Tamedevelopers\Validator\Methods\Constant;

class GetRequestType {
  
    /**
     * The value of request type.
     * 
     * @param string|null $request
     * @return int  
     */
    public static function request($request = null)
    {
        // Set default value for request type to POST
        $request = Str::lower($request);
        $requestStatus = Constant::POST;
        
        // always empty|null except `config_form()` has been used
        if(!empty($request)){
            if($request === 'all'){
                $requestStatus = self::fetchRequest(Http::method());
            } else{
                $requestStatus = self::fetchRequest($request);
            }
        }

        return $requestStatus;
    }

    /**
     * Fetch Requerst
     *
     * @param  string|null $request
     * @return int
     */
    private static function fetchRequest($request = null)
    {
        return match (Str::lower($request)) {
            'get'       => Constant::GET,
            'server'    => Constant::SERVER,
            'cookie'    => Constant::COOKIE,
            'request'   => Constant::REQUEST,
            default     => Constant::POST
        };
    }
    
}