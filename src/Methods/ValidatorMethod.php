<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Str;
use Tamedevelopers\Validator\Validator;
use Tamedevelopers\Support\Process\Http;
use Tamedevelopers\Validator\Methods\Constant;
use Tamedevelopers\Support\Collections\Collection;

class ValidatorMethod {

    /**
     * Instance of parent validator
     * 
     * @var Validator|null
     */
    public static ?Validator $validator = null;
    
    /**
     * Check if response is a JsonResponse instance.
     *
     * @param  mixed $response
     * @return bool
     */
    public static function isJsonResponse($response = null)
    {
        return !empty($response) 
            && $response instanceof \Symfony\Component\HttpFoundation\JsonResponse;
    }

    /**
     * Initialize methods to have access to global validator
     */
    public static function initialize(Validator $validator): void
    {
        self::$validator = $validator;
    }

    /**
     * Check if incoming param is set in superglobals.
     *
     * @param string|null $param
     * @return bool
     */
    public static function isParamSet($param = null)
    {
        $request = self::$validator->config['request'];

        return match ($request) {
            Constant::POST    => isset($_POST[$param]),
            Constant::GET     => isset($_GET[$param]),
            Constant::COOKIE  => isset($_COOKIE[$param]),
            Constant::SERVER  => isset($_SERVER[$param]),
            default => isset($_REQUEST[$param]),
        };
    }

    /**
     * Get and set the source parameter collection.
     *  
     * @param string|int $request
     * @return Validator
     */
    public static function getAndSetSourceParam($request = Constant::GET)
    {
        $param = match ($request) {
            Constant::POST    => $_POST,
            Constant::GET     => $_GET,
            Constant::COOKIE  => $_COOKIE,
            Constant::SERVER  => $_SERVER,
            Constant::REQUEST => $_REQUEST,
            default => self::createFromGlobals(),
        };
        
        // Convert param to collection
        self::$validator->param  = new Collection($param);
        
        return self::$validator;
    }

    /**
     * Create parameters from superglobals array.
     */
    public static function createFromGlobals(): array
    {
        return array_merge(
            $_POST,
            $_GET,
            $_REQUEST,
            $_COOKIE
        );
    }

    /**
     * Check if form data has been submitted.
     */
    public static function isSubmitted(): bool
    {
        $glob = self::globParam();

        return $glob instanceof Collection && $glob->count() > 0;
    }

    /**
     * Check if request is GET prior to form submission.
     */
    public static function isGetRequestBeforeSubmitted(): bool
    {
        return Str::lower(Http::method()) === 'get' && !self::isSubmitted();
    }

    /**
     * Extract specified keys from submitted params.
     * 
     * @param  array|null  $keys of input
     * @return array
     */
    public static function only($keys = null)
    {
        if ((!is_array($keys) || empty($keys))) {
            return [];
        }

        return array_intersect_key(self::globParam(true), array_flip($keys));
    }

    /**
     * Get all submitted params except specified keys.
     * 
     * @param  array|null $keys
     * @return array
     */
    public static function except($keys = null)
    {
        $glob = self::globParam(true);

        if ((!is_array($keys) || empty($keys))) {
            return $glob;
        }

        return array_diff_key($glob, array_flip($keys));
    }

    /**
     * Check if key exists in parameters collection.
     *
     * @param string|null $key
     * @return bool
     */
    public static function has($key = null)
    {
        if (is_null($key)) {
            return false;
        }

        return in_array($key, array_keys(self::globParam(true)));
    }

     /**
     * Merge two collections or arrays.
     *
     * @param array|Collection|null $keys
     * @param array|Collection|null $data
     * @return array
     */
    public static function merge($keys = null, $data = null)
    {
        $keys = self::isCollectionInstance($keys) ? $keys->toArray() : ($keys ?? []);
        $data = self::isCollectionInstance($data) ? $data->toArray() : ($data ?? []);
        
        return array_merge($keys,  $data);
    }

    /**
     * Extract specific keys from target dataset.
     * 
     * @param  array|Collection|null  $keys of needed data
     * @param  array|Collection|null  $data param to check from
     * @return array
     */
    public static function onlyData($keys = null, $data = null)
    {
        $keys = self::isCollectionInstance($keys) ? $keys->toArray() : ($keys ?? []);
        $data = self::isCollectionInstance($data) ? $data->toArray() : ($data ?? []);

        return array_intersect_key($data, array_flip($keys));
    }

    /**
     * Filter out specific keys from target dataset.
     * 
     * @param  array|Collection|null  $keys of data to remove from parameters
     * @param  array|Collection|null  $data param to check from
     * @return array
     */
    public static function exceptData($keys = null, $data = null)
    {
        $keys = self::isCollectionInstance($keys) ? $keys->toArray() : ($keys ?? []);
        $data = self::isCollectionInstance($data) ? $data->toArray() : ($data ?? []);
        
        return array_diff_key($data, array_flip($keys));
    }

    /**
     * Return previously entered value using dot notation or checkbox checks.
     * 
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function old($key = null, $default = null)
    {
        $data = self::getForm();
        $data = self::isCollectionInstance($data) ? $data->toArray() : $data;

        if ($key === null) {
            return $data;
        }

        $keySegments = explode('.', $key);

        foreach ($keySegments as $index => $segment) {
            if (is_array($data)) {
                // Case 1: Checkbox or indexed array check (e.g., old('activities.reading'))
                // If we're at the last segment and it exists as a VALUE inside $data
                if ($index === count($keySegments) - 1 && in_array($segment, $data, true)) {
                    return true; // means the checkbox was checked
                }

                // Case 2: Standard nested associative array traversal (e.g., old('user.name'))
                if (array_key_exists($segment, $data)) {
                    $data = $data[$segment];
                } else {
                    return $default;
                }
            } else {
                return $default;
            }
        }

        return $data ?? $default;
    }

    /**
     * Resolve flash message and save in memory
     * 
     * @param \Tamedevelopers\Validator\Validator|mixed $validator
     * @return mixed
     */
    public static function resolveFlash(Validator $validator)
    {
        // if message is an array
        if(is_array($validator->message)){
            foreach($validator->message as $message){ 
                $validator->flash['message'][] = $message; 
            }
        } else{
            $validator->flash['message'][] = $validator->message;
        }

        // configure error class
        self::configErrorClass($validator);

        // if form validation is successful
        if($validator->flashVerify){
            $validator->flash['class'] = $validator->class['success'];
        } else{
            // Set class to error
            $validator->flash['class'] = $validator->class['error'];
        }
        
        return self::$validator;
    }

    /**
     * Reset flash data to default values
     * 
     * @param \Tamedevelopers\Validator\Validator|mixed $validator
     * @return mixed
     */
    public static function resetFlash(Validator $validator)
    {
        $validator->flash = [
            'message'   => [],
            'class'     => '',
        ];
        
        return self::$validator;
    }

    /**
     * Get concatenated error message string.
     */
    public static function getMessage(): string
    {
        $message = !empty(self::$validator->message)
                ? self::$validator->message
                : self::$validator->flash['message'];

        // convert to array
        $message = !is_array($message) ? [$message] : $message;
                    
        return implode('<br>', $message);
    }

    /**
     * Get flash error class.
     */
    public static function getClass(): string
    {
        return (string) self::$validator->flash['class'];
    }

    /**
     * Retrieve single key or full form Collection.
     * 
     * @param string|null $key
     * @return mixed|Collection
     */
    public static function getForm($key = null)
    {
        $param = self::globParam();

        if ($key === null) {
            return $param;
        }

        return $param[$key] ?? $param;
    }

    /**
     * Get param data directly
     * 
     * @param string|null $key
     * @return mixed
     */
    public static function param($key = null)
    {
        $param = self::getForm($key);

        if(self::isCollectionInstance($param)){
            return $param->{$key};
        }

        return $param;
    }

    /**
     * Get combined $_GET and$_POST Collection.
     */
    public static function getAllForm(): Collection
    {
        return new Collection(array_merge(
            $_GET, $_POST
        ));
    }

    /**
     * Check if payload is an instance of Collection.
     * 
     * @param mixed $data
     * @return bool
     */ 
    public static function isCollectionInstance($data = null)
    {
        return ($data instanceof Collection);
    }

    /**
     * Return global param if set
     * 
     * @param bool $toArray
     * @return null|array|Collection
     */
    private static function globParam($toArray = false)
    {
        $param = self::$validator?->param ?? null;

        if(self::isCollectionInstance($param) && $toArray){
            return $param->toArray();
        }

        return $param;
    }

    /**
     * Load error class configuration from global constant if defined.
     *
     * @param  mixed $validator
     * @return mixed
     */
    private static function configErrorClass(&$validator)
    {
        if(defined('TAME_VALIDATOR_CONFIG')){
            $validator->class = TAME_VALIDATOR_CONFIG['class'];
        }

        return $validator->class;
    }

}