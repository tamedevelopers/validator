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

use Closure;
use Tamedevelopers\Support\Collections\Collection;
use Tamedevelopers\Validator\Interface\ValidatorInterface;
use Tamedevelopers\Validator\Methods\ValidatorMethod;
use Tamedevelopers\Validator\Traits\PropertyTrait;
use Tamedevelopers\Validator\Traits\ValidateSuccessTrait;
use Tamedevelopers\Validator\Traits\ValidatorTrait;

/**
 * Validator
 *
 * @package   tamedevelopers\validator
 * @author    Tame Developers <tamedevelopers@gmail.com>
 * @copyright 2021-2023 Tame Developers
 * @link https://github.com/tamedevelopers/validator
 */
class Validator implements ValidatorInterface
{
    use ValidatorTrait, 
        PropertyTrait,
        ValidateSuccessTrait;

    /**
     * @param  mixed $attribute     Additional Data usable within the form
     * 
     * @return void
     */
    public function __construct($attribute = null)
    {
        $this->attribute = new Collection($attribute);
        $this->message   = [];

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

        $this->initalizeAfterRequestSet();
        
        // set params
        $self = ValidatorMethod::getAndSetSourceParam($this->config['request']);

        // replace with parent params
        $this->param = $self->param;
    }

    /**
     * Create validation rules
     * 
     * @param  array{
     *  i: "['i|name'] => 'Int value is required'", 
     *  int: "['int|name'] => 'Int value is required'", 
     *  integer: "['integer|name'] => 'Int value is required'", 
     *  u: "['u|name'] => 'URL link is required'", 
     *  url: "['url|name'] => 'URL link is required'", 
     *  link: "['link|name'] => 'URL link is required'", 
     *  anchor: "['anchor|name'] => 'URL link is required'",
     *  e: "['e|name'] => 'Email address is required'", 
     *  email: "['email|name'] => 'Email address is required'", 
     *  a: "['a|name'] => 'Array value is required'", 
     *  array: "['array|name'] => 'Array value is required'", 
     *  b: "['b|name'] => 'Boolean value is required'", 
     *  bool: "['bool|name'] => 'Boolean value is required'", 
     *  boolean: "['boolean|name'] => 'Boolean value is required'", 
     *  en: "['en|name'] => 'Enum value is required'", 
     *  enum: "['enum|name'] => 'Enum value is required'", 
     *  s: "['s|name'] => 'String value is required'",
     *  string: "['string|name'] => 'String value is required'",
     *  html: "['html|name'] => 'HTML string value is required'", 
     *  raw: "['raw|name'] => 'Raw HTML string is required'", 
     *  dev: "['dev|name'] => 'Developer IDE-Raw string is required'", 
     *  sl: "['sl|name'] => 'String length is required'", 
     *  strlen: "['strlen|name'] => 'String length is required'", 
     *  str_len: "['str_len|name'] => 'String length is required'"
     * }|array<string, string> $rules Data Types
     * 
     * - Separator 
     *      (pipe) | or (colon) :
     * 
     * Supported operators
     * -------------------
     *  Equality:
     *      ==          loose equality
     *      ===         strict equality
     *      !=          loose inequality
     *      !==         strict inequality
     *
     *  Single comparison (numeric):
     *      >           greater than
     *      >=          greater than or equal
     *      <           less than
     *      <=          less than or equal
     *
     *  Exclusive range (bounds not included):
     *      <and>       lower < value < upper        (inside range)
     *      <or>        value < lower OR value > upper (outside range)
     *
     *  Inclusive range (bounds included):
     *      <=and>=     lower <= value <= upper      (inside range)
     *      <=or>=      value <= lower OR value >= upper (outside range)
     *
     *  Mixed bounds:
     *      <and>=      lower <  value <= upper
     *      <=and>      lower <= value <  upper
     *
     *  Step / multiple:
     *      step        value must be a multiple of the given step
     *      multiple    alias of `step`
     *
     *  Range + step (min,max,step):
     *      <and>step
     *      <and>:step
     *      steprange
     * 
     * @return $this
     * @example ["string:first_name" => "First name is required"]
     * @example [data_type|input_name|operator|value]
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
     * @param  Closure|null  $closure
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

            // run callback, which should return JsonResponse
            $response = $this->callback($closure);

            // Store whatever response-like object we got back,
            if (!is_null($response)) {
                $this->jsonResponse = $response;
            }
        }

        return $this;
    }
    
    /**
     * Form save response
     * 
     * @param  Closure  $closure
     * @return mixed
     */
    public function save($closure)
    {
        // If user returns a JsonResponse
        if (ValidatorMethod::isJsonResponse($this->jsonResponse)) {
            return $this->jsonResponse;
        }

        if($this->isValidated()){
            
            // save into a remembering variable
            ValidatorMethod::resolveFlash($this);
            
            $response = $this->callback($closure);

            // If user returns a JsonResponse in save, return it directly
            if (ValidatorMethod::isJsonResponse($response)) {
                return $response;
            }

            // If the callback returned something else (a Response, string, array)
            if (!is_null($response)) {
                return $response;
            }
        }

        return null;
    }
    
    /**
     * Before form submission 
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
     * After form has been submitted
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
     */
    public function hasError(): bool
    {
        return (!is_null($this->proceed) && $this->proceed === false);
    }

    /**
     * Check if Form has been validated
     * 
     * @return bool
     */
    public function isValidated()
    {
        $this->ignoreIfValidatorHasBeenCalled();

        return (!is_null($this->proceed) && $this->proceed);
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
     * @return array
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
     * Get Attribute Data
     * 
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Return previously entered value
     * 
     * @param string $key of param name
     * @param mixed $default
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
