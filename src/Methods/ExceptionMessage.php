<?php

declare(strict_types=1);

namespace Tamedevelopers\Validator\Methods;

class ExceptionMessage {
  
   /**
    * Return indicator `error` message
    * 
    * @return string 
    */
   public static function indicator()
   {
      return "Configuration error:: Indicator `:` type passed not valid";
   }
  
   /**
    * Return Comparison Operator `error` message
    * 
    * @return string 
    */
   public static function comparison(?array $dataType)
   {
      return sprintf("Comparison Operator error for this variable `%s`", $dataType['variable']);
   }
  
   /**
    * Return Variable Not found `error` message
    * 
    * @return string 
    */
   public static function notFound(?array $dataType)
   {
      return sprintf("Input Form `%s` not found with Form Data", $dataType['variable']);
   }
  
   /**
    * Csrf Token Mismatch `error` message
    * 
    * @return string 
    */
   public static function csrfTokenNotFound()
   {
      return "CSRF Token not Found.";
   }
  
   /**
    * Csrf Token Mismatch `error` message
    * 
    * @return string 
    */
   public static function csrfTokenMismatch()
   {
      return "CSRF Token Mismatch. Please refresh the page and try again.";
   }
    
}