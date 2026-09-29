<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

/**
 * Input source constants for validation.
 *
 * Provides standardized constants representing data input sources, mapping
 * directly to PHP input superglobals or custom request types.
 *
 * @package Tamedevelopers\Validator
 * @link https://github.com/tamedevelopers/validator
 */
class Constant {

    /**
     * POST request input source (`$_POST`).
     *
     * @var int
     */
    public const POST = INPUT_POST;

    /**
     * GET request query parameter source (`$_GET`).
     *
     * @var int
     */
    public const GET = INPUT_GET;

    /**
     * Cookie input source (`$_COOKIE`).
     *
     * @var int
     */
    public const COOKIE = INPUT_COOKIE;

    /**
     * Server and execution environment input source (`$_SERVER`).
     *
     * @var int
     */
    public const SERVER = INPUT_SERVER;

    /**
     * Combined or raw request input source (`$_REQUEST` / JSON body).
     *
     * @var int
     */
    public const REQUEST = 99; 

}