<?php

declare(strict_types=1);

namespace UltimateValidator;

trait PropertyTrait {
  
    /**
     * param
     */ 
    public  $param; 

    /**
     * attribute 
     * @var mixed
     */
    public $attribute;

    /**
     * public message notice
     * @var mixed
     */
    public  $message; 

    /**
     * data type
     * @var bool|null
     */
    public  $type; 

    /**
     * operator check
     */
    public  $operator; 

    /**
     * flash
     * @var array
     */
    public $flash = [
        'message'   => [],
        'class'     => '',
    ];

    /**
     * proceed
     * @var bool
     */
    public $flashVerify;

    /**
     * Message Error Type \Default is false
     * - true \This will return an array of error message
     * @var bool
     */
    private $errorType = false;

    /**
     * proceed
     * @var bool
     */
    private $proceed;

    /**
     * error 
     * @var bool
     */
    private $error = false;
    
    /**
     * Use Csrf Token
     * @var bool
     */
    private $allow_csrf = true;
    
}
