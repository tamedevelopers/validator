<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Process\Http;
use Tamedevelopers\Support\Str;

class GetRequestType {
  
    /**
     * The value of request type.
     * @param string|null $request
     * 
     * @return int  
     */
    public static function request($request = null)
    {
        // set default value for request type to POST
        $requestStatus = INPUT_POST;
        $request = Str::lower($request);
        
        // always empty|null except 
        // `config_form()` has been used
        if(!empty($request)){
            // convert any and get needed request
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
        $request = Str::lower($request);
        return match ($request) {
            'get'  => INPUT_GET,
            'post' => INPUT_POST,
            default => INPUT_POST
        };
    }
    
}