<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

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
            $requestStatus = match ($request) {
                'all', 'any' => INPUT_SERVER,
                'get'  => INPUT_GET,
                'post' => INPUT_POST,
                default => INPUT_POST
            };
        }

        return $requestStatus;
    }
    
}