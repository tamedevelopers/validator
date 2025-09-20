<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Traits;

trait PropertyTrait {
  
    /** @var mixed form param data */ 
    public $param; 

    /** @var mixed attribute */
    protected $attribute;

    /** @var mixed public message data */
    public $message;

    /** @var array Defined rules collectors */
    private $rules = [];

    /** @var \Symfony\Component\HttpFoundation\JsonResponse|null */
    public $jsonResponse;

    /** @var bool Keep track of manually calling the validate method */
    private $isValidatedCalled = false;
    
    /** @var bool proceed */
    public $flashVerify;

    /** @var bool proceed */
    private $proceed;

    /** @var bool error */
    private $error = false;

    /** @var array flash data */
    public $flash = [
        'message'   => [],
        'class'     => '',
    ];

    /** @var array style class */
    public $class = [
        'success'   => 'alert alert-success',
        'error'     => 'alert alert-danger',
    ];

    /** @var array error configuration */
    public $config = [
        'csrf'      => true,
        'operator'  => false,
        'errorType' => false,
        'request'   => false,
    ];
    
}
