<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

use Tamedevelopers\File\File;
use Tamedevelopers\Support\Purify;
use Tamedevelopers\Support\Str;
use Tamedevelopers\Support\Utility;
use Tamedevelopers\Validator\Validator;

class Datatype {
  
    // Rule name groups (constants instead of magic strings)
    private const RULE_EMAIL   = ['email', 'e'];
    private const RULE_INT     = ['int', 'integer', 'i'];
    private const RULE_FLOAT   = ['float', 'f', 'fl'];
    private const RULE_URL     = ['url', 'link', 'u', 'anchor'];
    private const RULE_ARRAY   = ['array', 'a'];
    private const RULE_BOOL    = ['bool', 'boolean', 'b'];
    private const RULE_ENUM    = ['enum', 'en', 'enm'];
    private const RULE_HTML    = ['html'];
    private const RULE_FILE    = ['file'];
    private const RULE_RAW     = ['raw'];
    private const RULE_DEV     = ['dev'];

    /**
     * Private instance of parent validator
     * 
     * @var mixed|Validator
     */
    private static $validator;

    /**
     * File Validator Message
     * 
     * @var array
     */
    private static ?array $files;

    /**
     * Validate if form data is set 
     * And if data type is correct
     * 
     * @param array $rulesData  [data_type|input_name|operator|value]
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
        $type           = true;
        $formValidated  = self::validateForminput($rulesData);
        $flagType       = Str::lower($rulesData['data_type']);
        $inputName      = $rulesData['input_name'];

        // check for ENUM types
        if(self::checkEnum($rulesData)){
            $type = $formValidated;
        } else{
            
            // check if value param isset
            if(ValidatorMethod::isParamSet($inputName)){
                $type = $formValidated;
            } 
            
            // if data passes is not in form elements
            elseif(!in_array($inputName, array_keys(self::$validator->param->toArray()))){

                // if file has error or not file
                if(!($flagType === 'file') || self::$files[$inputName]['error']){
                    $type = '!found';
                }
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

        $formValidated = self::validateForminput($rulesData);

        // Use strict comparison to check for validation failure
        // For boolean type, we need special handling
        if (self::boolComparison($dataType)) {
            if (!is_bool($formValidated)) {
                return $default;
            }

            return $formValidated;
        }

        // If validation fails, return default value
        if ($formValidated === false) {
            return $default;
        }

        return $formValidated;
    }

    /**
     * Get Files
     * 
     * @param string $name
     * @return \Tamedevelopers\File\File
     */
    public static function getFiles($name)
    {
        if(isset(self::$files[$name]['file'])){
            return self::$files[$name]['file'];
        }

        return File::name($name);
    }

    /**
     * Normalize Message
     *
     * @param mixed $message
     * @param mixed $dataType
     * @param mixed $inputName
     * @return string|null
     */
    public static function normalizeMessage($message, $dataType, $inputName)
    {
        if($dataType === 'file' && self::$files[$inputName]['error']){
            return self::$files[$inputName]['message'];
        }

        return $message;
    }

    /**
     * Check if data type is boolean
     *
     * @param string|null $dataType Data type to check
     * @return bool
     */
    public static function boolComparison(?string $dataType = null)
    {
        return $dataType && in_array(strtolower($dataType), self::RULE_BOOL, true);
    }

    /**
     * Validate form input
     *
     * @param array $rulesData  [data_type|input_name|operator|value]
     * @return mixed
     */
    protected static function validateFormInput(array $rulesData)
    {
        $flagType  = Str::lower($rulesData['data_type']);
        $inputName = $rulesData['input_name'];
        $value     = self::$validator->param[$inputName] ?? null;

        switch (true) {
            // ---------------- FILE ----------------
            case in_array($flagType, self::RULE_FILE, true):
                return self::fileValidator($inputName);

            // ---------------- EMAIL ----------------
            case in_array($flagType, self::RULE_EMAIL, true):
                $valid = Utility::validateEmail($value);
                return $valid ? Purify::string((string) $value) : false;

            // ---------------- INTEGER ----------------
            case in_array($flagType, self::RULE_INT, true):
                // strip everything except digits (add \- if you want negatives)
                $clean = preg_replace('/[^0-9]/', '', (string) $value);

                if ($clean === '') {
                    return false;
                }

                // FILTER_VALIDATE_INT still guards against overflow / huge strings
                $valid = filter_var($clean, FILTER_VALIDATE_INT);
                return $valid !== false ? (int) $valid : false;

            // ---------------- FLOAT ----------------
            case in_array($flagType, self::RULE_FLOAT, true):
                // keep digits, dot, minus
                $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);

                // ensure only one leading minus
                $negative = str_starts_with($clean, '-');
                $clean    = str_replace('-', '', $clean);

                // ensure only one dot
                $parts = explode('.', $clean);
                $clean = $parts[0] . (isset($parts[1]) ? '.' . $parts[1] : '');

                if ($clean === '' || $clean === '.') {
                    return false;
                }

                if ($negative) {
                    $clean = '-' . $clean;
                }

                $valid = filter_var($clean, FILTER_VALIDATE_FLOAT);
                return $valid !== false ? (float) $valid : false;

            // ---------------- URL ----------------
            case in_array($flagType, self::RULE_URL, true):
                if ($value && !preg_match('/^https?:\/\//i', $value)) {
                    $value = 'http://' . $value;
                }
                $valid = filter_var($value, FILTER_VALIDATE_URL);
                return $valid ? Purify::string((string) $valid) : false;

            // ---------------- ARRAY ----------------
            case in_array($flagType, self::RULE_ARRAY, true):
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
            case in_array($flagType, self::RULE_BOOL, true):
                $valid = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                return $valid === null ? false : $valid;

            // ---------------- ENUM ----------------
            case in_array($flagType, self::RULE_ENUM, true):
                if ($value === null || ($value === '' && $value !== '0')) {
                    return false;
                }
                return Purify::string((string) $value);

            // ---------------- HTML ----------------
            case in_array($flagType, self::RULE_HTML, true):
                $sanitized = Purify::html((string) $value);
                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;

            // ---------------- Raw ----------------
            case in_array($flagType, self::RULE_RAW, true):
                $sanitized = Purify::raw((string) $value);

                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;

            // ---------------- DEV ----------------
            case in_array($flagType, self::RULE_DEV, true):
                $sanitized = Purify::dev((string) $value);
                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;

            // ---------------- DEFAULT STRING ----------------
            default:
                $sanitized = Purify::string((string) $value);
                return (empty($sanitized) && $sanitized !== '0') ? false : $sanitized;
        }
    }

    /**
     * Validate file error only
     *
     * @param mixed $inputName
     * @return bool
     */
    protected static function fileValidator($inputName)
    {
        $file       = self::$validator->file($inputName);
        $formRules  = self::normalizeFileRules()[$inputName] ?? [];

        // if the incoming request is File instance
        $isFileInstance = isset($formRules['fileInstance']) 
            && isset($formRules['file']) 
            && self::fileInstance($formRules['file']);

        if($isFileInstance){
            $file = $formRules['file'];
        } else{
            foreach ($formRules as $method => $methodValue) {
                if (method_exists($file, $method)) {
                    $params = is_array($methodValue) ? $methodValue : [$methodValue];
    
                    $file->$method(...$params);
                }
            }
        }

        // Validate without saving the file
        $validate = $file->validate();

        // Store validation result
        self::$files[$inputName] = [
            'file'    => $file,
            'error'   => $validate->hasError(),
            'message' => $validate->getMessage(),
        ];

        return self::$files[$inputName]['error'];
    }

    /**
     * Normalize File Rules
     */
    protected static function normalizeFileRules(): array
    {
        $fileRules = [];

        foreach (self::$validator->getRules() as $key => $value) {
            if (!str_starts_with($key, 'file:')) {
                continue;
            }

            $field = substr($key, strlen('file:'));

            // if instance of File
            if(self::fileInstance($value)){
                $fileRules[$field] = [
                    'file' => $value,
                    'fileInstance' => true,
                ];
                continue;
            }

            foreach (explode('|', $value) as $rule) {
                if (!str_contains($rule, ':')) {
                    continue;
                }

                [$name, $params] = explode(':', $rule, 2);

                $params = array_map(
                    static function ($param) {
                        $param = trim($param);

                        return match (strtolower($param)) {
                            'true'  => true,
                            'false' => false,
                            default => is_numeric($param) ? $param + 0 : $param,
                        };
                    },
                    explode(',', $params)
                );

                $fileRules[$field][$name] = count($params) === 1
                    ? $params[0]
                    : $params;
            }
        }

        return $fileRules;
    }

    /**
     * File Instance
     *
     * @param mixed $value
     * @return bool
     */
    protected static function fileInstance($value): bool
    {
        $fileClass = 'Tamedevelopers\File\File';

        return $value instanceof $fileClass;
    }

    /**
     * Majorly for Form's Radio/Checkbox
     * If either of those input type is not checked yet, input not send along form
     * So we need determine if it should be set by the system, or not.
     * 
     * @param  array $rulesData  [data_type|input_name|operator|value]
     * @return bool
     */
    protected static function checkEnum($rulesData)
    {
        return in_array(strtolower($rulesData['data_type']), ['enum', 'en', 'enm']);
    }

}