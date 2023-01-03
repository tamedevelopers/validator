<?php

declare(strict_types=1);

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
 * ExceptionMessage
 *
 * @package   Ultimate\Validator
 * @author    Tame Developers <tamedevelopers@gmail.co>
 * @copyright 2021-2023 Tame Developers
 */
class ExceptionMessage {
  
   /**
    * Return indicator `error` message
    * 
    * @return string\exception 
    */
   public static function indicator()
   {
      return "Configuration error:: Indicator `:` type passed not valid";
   }
  
   /**
    * Return Comparison Operator `error` message
    * 
    * @return string\exception 
    */
   public static function comparison(?array $dataType)
   {
      return sprintf("Comparison Operator error for this variable `%s`", $dataType['variable']);
   }
  
   /**
    * Return Variable if not found `error` message
    * 
    * @return string\exception 
    */
   public static function notFound(?array $dataType)
   {
      return sprintf("Variable or Input Form `%s` is not set or name not found along with Form Data", $dataType['variable']);
   }
    
}