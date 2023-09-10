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

use UltimateValidator\Collections\Collection;
use UltimateValidator\Interface\UltimateInterface;


/**
 * Validator
 *
 * @package   Ultimate\Validator
 * @author    Tame Developers <tamedevelopers@gmail.co>
 * @author    Fredrick Peterson <fredi.peterson2000@gmail.com>
 * @copyright 2021-2023 Tame Developers
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
class UltimateValidator implements UltimateInterface
{

    use ValidatorTrait, 
        PropertyTrait,
        SubmitSuccessTrait;
    
    
    /**
     * @param  mixed $attribute
     * @param  mixed $attribute
     * - Any outside parameter you would want to use within the form instance
     * 
     * @return void
     */
    public function __construct(mixed $attribute = null) 
    {
        $this->message      = [];
        $this->type         = $this->getType();
        $this->attribute    = new Collection($attribute);
        
        // initialize methods
        UltimateMethods::initialize($this);
        
        // set params
        UltimateMethods::setParams($this->type);

        // if defined
        if(defined('GLOBAL_FORM_CSRF_TOKEN')){
            $this->allow_csrf = GLOBAL_FORM_CSRF_TOKEN;
        }

        // if defined
        if(defined('GLOBAL_FORM_ERROR')){
            $this->errorType = GLOBAL_FORM_ERROR;
        }
    }

    /**
     * Create Form Validation Data
     * 
     * @param  array $data
     * 
     * @return $this
     */
    public function submit(?array $data = []) 
    {
        return $this->submitInitialization($data);
    }

    /**
     * @param  callable  $function.
     * @param  null|void  pass a $var into the funtion to access the returned object
     * usage   ->error(  function($response){}  );
     * 
     * @return $this
     */
    public function error(?callable $function)
    {
        if(!is_null($this->proceed) && $this->proceed === false){
            if(is_callable($function)){
                $function($this);

                // save into a remembering variable
                UltimateMethods::resolveFlash($this);
            }
        }
        return $this;
    }
    
    /**
     * @param  callable  $function.
     * @param  null|void  pass a $var into the funtion to access the returned object
     * usage   ->success(  function($response){}  );
     * 
     * @return $this
     */
    public function success(?callable $function)
    {
        if(!is_null($this->proceed) && $this->proceed){
            if(is_callable($function)){
                $function($this);

                // save into a remembering variable
                UltimateMethods::resolveFlash($this);
            }
        }
        return $this;
    }
    
    /**
     * @param  callable  $function.
     * Before form is set
     * usage   ->beforeSubmit(  function($response){}  );
     * 
     * @return $this
     */
    public function beforeSubmit($function)
    {
        // reset data
        UltimateMethods::resetFlash($this);

        if(UltimateMethods::isRequestMethod()){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }

    /**
     * @param  callable  $function.
     * After form is set
     * usage   ->afterSubmit(  function($response){}  );
     * 
     * @return $this
     */
    public function afterSubmit($function)
    {
        // reset data
        UltimateMethods::resetFlash($this);
        
        if(UltimateMethods::isSubmitted()){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }

    /**
     * Needed input values from submited form object
     * @param  array|null  $keys of input
     * 
     * @return array
     */
    public function only($keys = null)
    {
        return UltimateMethods::only($keys);
    }

    /**
     * Remove input values from submited form object
     * @param  array|null  $keys of input
     * 
     * @return array
     */
    public function except($keys = null)
    {
        return UltimateMethods::except($keys);
    }

    /**
     * Check if param is set in parent param
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(?string $key = null)
    {
        return UltimateMethods::has($key);
    }

    /**
     * Remove value of parameters form objects
     *
     * @param array $keys
     *
     * @return array
     */
    public function merge(?array $keys = null, ?array $data = null)
    {
        return UltimateMethods::merge($keys, $data);
    }

    /**
     * Return previously entered value
     * 
     * @param string $key of param name
     * 
     * @return mixed
     */
    public function old($key = null)
    {
        return UltimateMethods::old($key);
    }

}
