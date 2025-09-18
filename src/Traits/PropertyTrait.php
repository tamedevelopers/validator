<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Traits;

trait PropertyTrait {
  
    /**
     * param
     */ 
    public $param; 

    /**
     * attribute 
     * @var mixed
     */
    public $attribute;

    /**
     * public message notice
     * @var mixed
     */
    public $message;

    /** @var \Symfony\Component\HttpFoundation\JsonResponse|null */
    public $jsonResponse = null;

    /** Flag to avoid double-sending responses */
    protected bool $responseSent = false;

    /**
     * Defined rules collectors
     *
     * @var array
     */
    private $rules = [];

    /**
     * Keep track of manually calling the validate method
     *
     * @var bool
     */
    private $isValidatedCalled = false;
    

    /**
     * proceed
     * @var bool
     */
    public $flashVerify;

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
     * flash
     * @var array
     */
    public $flash = [
        'message'   => [],
        'class'     => '',
    ];

    /**
     * error class
     * @var array
     */
    public $class = [
        'success'   => 'alert alert-success',
        'error'     => 'alert alert-danger',
    ];

    /**
     * error class
     * @var array
     */
    public $config = [
        'csrf'      => true,
        'operator'  => false,
        'errorType' => false,
        'request'   => false,
    ];
    
}
