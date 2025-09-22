<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Purify;
use Tamedevelopers\Support\Utility;

class Datatype {
  
    // Rule name groups (constants instead of magic strings)
    private const RULE_EMAIL   = ['email', 'e'];
    private const RULE_INT     = ['int', 'integer', 'i'];
    private const RULE_FLOAT   = ['float', 'f'];
    private const RULE_URL     = ['url', 'link', 'u', 'anchor'];
    private const RULE_ARRAY   = ['array', 'a'];
    private const RULE_BOOL    = ['bool', 'b'];
    private const RULE_ENUM    = ['enum', 'en', 'enm'];
    private const RULE_HTML    = ['html'];
    private const RULE_RAW     = ['raw'];
    private const RULE_DEV     = ['dev'];

    /**
     * Private instance of parent validator
     * 
     * @var mixed
     */
    private static $validator;

    /**
     * Validate if form data is set 
     * And if data type is correct
     * 
     * @param array $rulesData 
     * - [data_type|input_name|operator|value]
     * 
     * - !isset if param is not set
     * - false if expected data type is not correct
     * - true|data on success
     * 
     * @return mixed
     */
    public static function validate($rulesData)
    {
        // parent object
        self::$validator = ValidatorMethod::$validator;

        // if input parameter is isset -- proceed to error validating
        $type = true;

        // check for ENUM types
        if(self::checkEnum($rulesData)){
            $type = self::validateForminput($rulesData);
        }

        else{
            
            // check if value param isset
            if(ValidatorMethod::checkIfParamIsset($rulesData['input_name'])){
                $type = self::validateForminput($rulesData);
            } 
            
            // if data passes is not in form elements
            elseif(!in_array($rulesData['input_name'], array_keys(self::$validator->param->toArray()))){
                $type = '!found';
            }

            // else if not isset
            else{
                $type = '!isset';
            }
        }

        return $type;
    }

    /**
     * Get form input safely
     * 
     * @param  string $inputName  Name of the input
     * @param  string|null $dataType  Optional type for validation/purification
     * @param  mixed $default  Default value if input not set
     * 
     * @return mixed
     */
    public static function getFormInput(string $inputName, ?string $dataType = null, $default = null)
    {
        // Check if input exists in validator param
        $value = self::$validator->param[$inputName] ?? $default;

        // If no data type specified, return raw value
        if (!$dataType) {
            return $value;
        }

        // Apply validation/purification based on type
        $rulesData = [
            'data_type' => $dataType,
            'input_name' => $inputName,
        ];

        $validated = self::validateForminput($rulesData);

        // If validation fails, return default value
        if ($validated === false) {
            return $default;
        }

        return $validated;
    }

    /**
     * Validate form input
     *
     * @param array $rulesData
     * - [data_type|input_name|operator|value]
     *
     * @return string|int|float|bool|array
     */
    protected static function validateFormInput(array $rulesData)
    {
        $ruleFlag  = Str::lower($rulesData['data_type']);
        $ruleInput = $rulesData['input_name'];
        $value     = self::$validator->param[$ruleInput] ?? null;

        switch (true) {

            // ---------------- EMAIL ----------------
            case in_array($ruleFlag, self::RULE_EMAIL, true):
                $valid = Utility::validateEmail($value);
                return $valid ? Purify::string((string) $value) : false;

            // ---------------- INTEGER ----------------
            case in_array($ruleFlag, self::RULE_INT, true):
                $valid = filter_var($value, FILTER_VALIDATE_INT);
                return $valid !== false ? intval($valid) : false;

            // ---------------- FLOAT ----------------
            case in_array($ruleFlag, self::RULE_FLOAT, true):
                $valid = filter_var($value, FILTER_VALIDATE_FLOAT);
                return $valid !== false ? floatval($valid) : false;

            // ---------------- URL ----------------
            case in_array($ruleFlag, self::RULE_URL, true):
                if ($value && !preg_match('/^https?:\/\//i', $value)) {
                    $value = 'http://' . $value;
                }
                $valid = filter_var($value, FILTER_VALIDATE_URL);
                return $valid ? Purify::string((string) $valid) : false;

            // ---------------- ARRAY ----------------
            case in_array($ruleFlag, self::RULE_ARRAY, true):
                $array = is_string($value) ? json_decode($value, true) : $value;

                $sanitizeArray = function ($arr) use (&$sanitizeArray) {
                    return array_map(fn($v) =>
                        is_array($v) ? $sanitizeArray($v) :
                        (is_string($v) ? Purify::string((string) $v) : $v), $arr);
                };

                return (is_array($array) && count($array) > 0)
                    ? $sanitizeArray($array)
                    : false;

            // ---------------- BOOLEAN ----------------
            case in_array($ruleFlag, self::RULE_BOOL, true):
                $valid = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                return $valid === null ? false : $valid;

            // ---------------- ENUM ----------------
            case in_array($ruleFlag, self::RULE_ENUM, true):
                if ($value === null || ($value === '' && $value !== '0')) {
                    return false;
                }
                return Purify::string((string) $value);

            // ---------------- HTML ----------------
            case in_array($ruleFlag, self::RULE_HTML, true):
                $sanitized = Purify::html((string) $value);
                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;

            // ---------------- Raw ----------------
            case in_array($ruleFlag, self::RULE_RAW, true):
                $sanitized = Purify::raw((string) $value);
                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;

            // ---------------- DEV ----------------
            case in_array($ruleFlag, self::RULE_DEV, true):
                $sanitized = Purify::dev((string) $value);
                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;

            // ---------------- DEFAULT STRING ----------------
            default:
                $sanitized = Purify::string((string) $value);
                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;
        }
    }

    /**
     * Majorly for Form's Radio/Checkbox
     * If either of those input type is not checked yet, input not send along form
     * So we need determine if it should be set by the system, or not.
     * 
     * @param  array $rulesData
     * - [data_type|input_name|operator|value]
     * 
     * @return bool
     */
    protected static function checkEnum($rulesData)
    {
        return in_array(strtolower($rulesData['data_type']), ['enum', 'en', 'enm']);
    }

}