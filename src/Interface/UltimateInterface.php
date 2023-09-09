<?php

declare(strict_types=1);

namespace UltimateValidator\Interface;

interface UltimateInterface 
{
    
    /**
     * Return value of needed parameters form objects
     *
     * @param array|null $keys
     *
     * @return array
     */
    public function only(?array $keys = null);

    /**
     * Remove value of parameters form objects
     *
     * @param array|null $keys
     *
     * @return array|null
     */
    public function except(?array $keys = null);

    /**
     * Check if param is set in parent param
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(?string $key = null);

    /**
     * Remove value of parameters form objects
     *
     * @param array $keys
     *
     * @return array
     */
    public function merge(?array $keys = null, ?array $data = null);

    
}