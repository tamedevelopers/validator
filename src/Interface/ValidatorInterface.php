<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Interface;


interface ValidatorInterface 
{
    
    /**
     * Return value of needed param from Form
     *
     * @param array|null $keys
     * @return array
     */
    public function only($keys = null);

    /**
     * Remove value of param from Form
     *
     * @param array|null $keys
     * @return array|null
     */
    public function except($keys = null);

    /**
     * Check if Form has a param
     *
     * @param string|null $key
     * @return bool
     */
    public function has($key = null);

    /**
     * Merge `keys` value to Form param
     *
     * @param array|null $keys
     * @param array|null $data
     *
     * @return array
     */
    public function merge($keys = null, $data = null);

    
}