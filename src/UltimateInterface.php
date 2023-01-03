<?php

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
 * UltimateInterface
 *
 * @package   Ultimate\Validator
 * @author    Tame Developers <tamedevelopers@gmail.co>
 * @copyright 2021-2023 Tame Developers
 */
interface UltimateInterface 
{
    
    /**
     * Return value of needed parameters form objects
     *
     * @param array|null\keys $keys
     *
     * @return array
     */
    public function only(?array $keys = null);

    /**
     * Remove value of parameters form objects
     *
     * @param array|null\keys $keys
     *
     * @return array|null
     */
    public function except(?array $keys = null);

    /**
     * Check if param is set in parent param
     *
     * @param string\key $key
     *
     * @return boolean
     */
    public function has(?string $key = null);

    /**
     * Remove value of parameters form objects
     *
     * @param array\keys $keys
     *
     * @return array
     */
    public function merge(?array $keys = null, ?array $data = null);

    
}