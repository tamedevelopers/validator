<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Collections;


use ArrayAccess;
use Traversable;
use ArrayIterator;
use IteratorAggregate;

class Collection implements IteratorAggregate, ArrayAccess
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new collection.
     *
     * @param  array $items
     */
    public function __construct($items = [])
    {
        $this->items = $this->convertOnInit($items);
    }
    
    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->__unset($offset);
    }

    /**
     * Determine if the collection has a given key.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Dynamically access collection items.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return  $this->items[$key] ?? null;
    }

    /**
     * Dynamically set an item in the collection.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * Check if an item exists in the collection.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->items[$key]);
    }

    /**
     * Remove an item from items collection.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return is_array($this->items) 
                ? count($this->items)
                : 0;
    }

    /**
     * Convert data to array
     * 
     * @return array
     */ 
    public function toArray()
    {
        return is_array($this->items)
                ? $this->items
                : [];
    }
    
    /**
     * Convert data to object
     * 
     * @return object
     */ 
    public function toObject()
    {
        return json_decode( json_encode($this->items), false);
    }
    
    /**
     * Convert data to json
     * 
     * @return string
     */ 
    public function toJson()
    {
        return json_encode($this->items);
    }

    /**
     * Convert data to an array on Initializaiton
     * @param mixed $items
     * 
     * @return array
     */ 
    private function convertOnInit(mixed $items = null)
    {
        return json_decode( json_encode($items), true);
    }

}