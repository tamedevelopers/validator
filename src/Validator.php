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

namespace Tamedevelopers\Validator;

use Tamedevelopers\Support\Collections\Collection;
use Tamedevelopers\Validator\Traits\PropertyTrait;
use Tamedevelopers\Validator\Traits\ValidatorTrait;
use Tamedevelopers\Validator\Methods\ValidatorMethod;
use Tamedevelopers\Validator\Traits\ValidateSuccessTrait;
use Tamedevelopers\Validator\Interface\ValidatorInterface;
use Tamedevelopers\Validator\Methods\CsrfToken;

/**
 * Validator
 *
 * @package   tamedevelopers\validator
 * @author    Tame Developers <tamedevelopers@gmail.com>
 * @copyright 2021-2023 Tame Developers
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/tamedevelopers/validator
 */
class Validator implements ValidatorInterface
{

    use ValidatorTrait, 
        PropertyTrait,
        ValidateSuccessTrait;
    
    /**
     * @param  mixed $attribute
     * - Any outside parameter you would want to use within the form instance
     * 
     * @return void
     */
    public function __construct($attribute = null) 
    {
        $this->attribute        = new Collection($attribute);
        $this->message          = [];

        // if defined
        if(defined('TAME_VALIDATOR_CONFIG')){
            $const = TAME_VALIDATOR_CONFIG;
            $this->config['class']      = $const['class'];
            $this->config['request']    = $this->getFormRequest($const['request']);
            $this->config['errorType']  = $const['error_type'];
            $this->config['csrf']       = $const['csrf_token'];
        } else{
            $this->config['request'] = $this->getFormRequest();
        }

        // initialize methods
        ValidatorMethod::initialize($this);
        
        // set params
        ValidatorMethod::setParams($this->config['request']);
    }

    /**
     * Create validation rules
     * 
     * @param  array $rules
     * - Separator <: or |>
     * - [data_type|input_name|operator|value]
     * 
     * - Data Types [<int/i/integer>|<float/f>|<email/e>|<url/u/link>|<array/a>|<bool/b>|<enum/en/enm>|<string/s>]
     * 
     * - Operators [==,===,!=,!==,>,>=,<,<=,<or>,<and>]
     * 
     * - example["string:first_name" => "First name is required"]
     * 
     * @return $this
     * @link https://github.com/tamedevelopers/validator
     */
    public function rules(?array $rules = []) 
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Begin form validation
     * 
     * @param  Closure|null  $function
     * @return $this
     */
    public function validate($closure = null)
    {
        // validate rules
        $this->validateRules();

        // validation has been called
        // this helps us to keep track if to call in the future instance or not
        $this->isValidatedCalled = true;
        
        if($this->hasError()){

            // save into a remembering variable
            ValidatorMethod::resolveFlash($this);

            $this->callback($closure);
        }

        return $this;
    }
    
    /**
     * Form save response
     * 
     * @param  Closure  $function
     * @return $this
     */
    public function save($closure)
    {
        if($this->isValidated()){
            
            // save into a remembering variable
            ValidatorMethod::resolveFlash($this);
            
            $this->callback($closure);
            
            // delete csrf session token
            CsrfToken::unsetToken();
        }

        return $this;
    }
    
    /**
     * Before form submission 
     * - [GET] request type only allowed
     * 
     * @param  Closure  $closure.
     * @return $this
     */
    public function before($closure)
    {
        // reset data
        ValidatorMethod::resetFlash($this);

        if(ValidatorMethod::isGetRequestBeforeSubmitted()){
            $this->callback($closure);
        }

        return $this;
    }

    /**
     * After form submission
     * - [All] request type allowed
     * 
     * @param  Closure  $closure.
     * @return $this
     */
    public function after($closure)
    {
        // reset data
        ValidatorMethod::resetFlash($this);
        
        if(ValidatorMethod::isSubmitted()){
            $this->callback($closure);
        }
        return $this;
    }

    /**
     * Check if Form has validation errors
     * 
     * @return bool
     */
    public function hasError()
    {
        if(!is_null($this->proceed) && $this->proceed === false){
            return true;
        }

        return false;
    }

    /**
     * Check if Form has been validated
     * 
     * @return bool
     */
    public function isValidated()
    {
        $this->ignoreIfValidatorHasBeenCalled();

        if(!is_null($this->proceed) && $this->proceed){
            return true;
        }

        return false;
    }

    /**
     * Return value of needed param from Form
     *
     * @param array|null $keys
     * @return array
     */
    public function only($keys = null)
    {
        return ValidatorMethod::only($keys);
    }

    /**
     * Remove value of param from Form
     *
     * @param array|null $keys
     * @return array|null
     */
    public function except($keys = null)
    {
        return ValidatorMethod::except($keys);
    }

    /**
     * Check if Form has a param
     *
     * @param string|null $key
     * @return bool
     */
    public function has($key = null)
    {
        return ValidatorMethod::has($key);
    }

    /**
     * Merge `keys` value to Form param
     *
     * @param array|null $keys
     * @param array|null $data
     *
     * @return array
     */
    public function merge($keys = null, $data = null)
    {
        return ValidatorMethod::merge($keys, $data);
    }

    /**
     * Return previously entered value
     * 
     * @param string $key of param name
     * 
     * @param mixed $default
     * [optional] 
     * 
     * @return mixed
     */
    public function old($key = null, $default = null)
    {
        return ValidatorMethod::old($key, $default);
    }

    /**
     * Reset Error from Success to Error Class
     * 
     * @return void
     */
    public function reset()
    {
        $this->flashVerify = false;
        $this->flash['class'] = $this->class['error'];
    }

}
