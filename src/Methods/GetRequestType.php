<?php

declare(strict_types=1);

/*
 * This file is part of ultimate-validator.
 *
 * (c) Tame Developers Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UltimateValidator;

/**
 * GetRequestType
 *
 * @package   Ultimate\Validator
 * @author    Tame Developers <tamedevelopers@gmail.co>
 * @copyright 2021-2023 Tame Developers
 */
class GetRequestType {
  
    /**
     * The value of request type.
     * 
     * @param  string $type    string like POST or GET.
     * @return int\request  
     */
    public static function request($type = null)
    {
        // if type is not passed then we detect the method type
        if(empty($type) || !in_array($type, ['get', 'GET', 'post', 'POST'])){
            $type = $_SERVER['REQUEST_METHOD'];
        }

        switch(strtolower($type)){
            case 'get':
                $request = INPUT_GET;
                break;

            default:
                $request = INPUT_POST;
                break;
        }
        return $request;
    }
    
}