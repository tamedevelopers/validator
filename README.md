# PHP Form Validator  - PFV

[![Total Downloads](https://poser.pugx.org/tamedeveloper/validator/downloads)](https://packagist.org/packages/tamedeveloper/validator)
[![Latest Stable Version](https://poser.pugx.org/tamedeveloper/validator/version.png)](https://packagist.org/packages/tamedeveloper/validator)
[![License](https://poser.pugx.org/tamedeveloper/validator/license)](https://packagist.org/packages/tamedeveloper/validator)
[![Build Status](https://github.com/tamedevelopers/validator/actions/workflows/php.yml/badge.svg)](https://github.com/tamedevelopers/validator/actions)
[![Code Coverage](https://codecov.io/gh/tamedeveloper/validator/branch/2.2.x/graph/badge.svg)](https://codecov.io/gh/tamedeveloper/validator/branch/3.2.2.x)

## Documentation

* [Requirements](#requirements)
* [Installation](#installation)
* [Instantiate](#instantiate)
* [Laravel Support](#laravel-support)
* [Methods That Should Always Come First](#methods-that-should-always-come-first)
* [Global Configuration](#methods-that-should-always-come-first)
* [Csrf](#csrf)
    * [Csrf Form Input](#csrf-form-input)
    * [Csrf Token](#csrf-token)
* [Usage](#usage)
  * [Error Type](#error-type)
  * [Token](#token)
  * [POST](#post)
  * [GET](#get)
  * [ALL](#all)
  * [Rules](#rules)
  * [Validate](#validate)
  * [Success](#success)
  * [Data Flags](#data-flags)
  * [Operators](#operators)
  * [noInterface](#nointerface)
  * [Before](#before)
  * [After](#after)
* [Reset Error](#reset-error)
* [Only](#only)
* [Except](#except)
* [Has](#has)
* [Old](#old)
* [Merge](#merge)
* [onlyData](#onlydata-method)
* [ExceptData](#exceptdata-method)
* [GetForm](#getForm)
* [Get Message and Class](#get-message-and-class)
* [Collection](#collection)
* [Collection Methods](#collection-methods)
* [toObject](#toobject)
* [toArray](#toarray)
* [toJson](#tojson)
* [Helpers](#helpers)
* [Useful links](#useful-links)

## Requirements

- `>= php 8.0+`

## Installation

Prior to installing `validator package` get the [Composer](https://getcomposer.org) dependency manager for PHP because it'll simplify installation.

```
composer require tamedevelopers/validator
```

## Instantiate — `Instantiate class using`
```
require_once __DIR__ . '/vendor/autoload.php';

use \Tamedevelopers\Validator\Validator;

$form = new Validator();
```

- or -- `Helpers Function`
```
$form = form();
```

## Laravel Support
- Now supports Laravel and with same Functionalities no different
    - `use Tamedevelopers\Validator\Validator;`

```
public function save(Request $request){

    $form = new Validator();
    or
    $form = form();
}
```

## Methods That Should Always Come First
- All are Optional `method`
    - These methods are only mandatory on usage and should always come first before others.

| Methods       |        Description                                                        |
|---------------|---------------------------------------------------------------------------|
| ->errorType() |  `bool` to format error's on display: `single or multiple`                |
| ->token()     |  `bool` to Enable or Disable `csrf_token` for each request                |
| ->post()      |  Convert Form request to `POST` only                                      |
| ->get()       |  Convert Form request to  `GET` only                                      |
| ->all()       |  Convert Form request to `any`                                            |

```
$form->post()->submit([
    // 
]);
```

## Global Configuration
- Helpers available to assist on easy configuration
    - `config_form()`

| Array Keys  |        Description                      |
|-------------|-----------------------------------------|
| request     |  String `POST\|GET\|ALL` Default `POST` |
| error_type  |  Boolean `true\|false` Default `false`  |
| csrf_token  |  Boolean `true\|false` Default `true`   |
| class       |  Array `error\|success` error class type to be returned on both success and failure   |

```
config_form([
    'request'       => 'POST',
    'error_type'    => true,
    'csrf_token'    => true,
    'class'         => [
        'error'     => 'alert alert-danger',
        'success'   => 'alert alert-success'
    ]
]); 
```

## Csrf
- Implementing `Csrf` (Cross-Site Request Forgery)
    - By default the form requires all request to have a token attached.
        - You can disable the usage with the `config_form()` Helper


### Csrf Form Input
- This will create html input element with valid `csrf token`
    - It's a function and you don't need to `echo`
        - Use anywhere inside your HTML form

```
csrf();
```
![Sample Csrf Form Input](https://raw.githubusercontent.com/tamedevelopers/validator/main/getErrorMessage.png)

### Csrf Token
- This will return the `csrf token` string

```
csrf_token();
```

## USAGE
- All Methods of usage 


### Error Type
- Takes a param as `bool` Default is `false`
    - You can call separately or Call Before any other method, if intend to use.

| Error |        Description            |
|-------|-------------------------------|
| false |  `Default` Errors displayed one after another  |
| true  |  This allow all errors to be displayed once, `as an array` |

```
$form->errorType(false);
```

- or
```
$form->et(true)->submit([
    // 
])
```

### Token
- Takes a param as `bool` Default is `false`
    - Allow disability of `csrf_token` on each form request

| Error |        Description                            |
|-------|-----------------------------------------------|
| false |  `Default` Will disable `csrf_token` usage    |
| true  |  This allow `csrf_token` per request only     |

```
$form->token(false);

$form->csrf(false);
```

### POST
- Set the Form Request to `POST`
    - This will always override the `config_form()` settings

```
$form->post();
```

### GET
- Set the Form Request to `GET`

```
$form->get();
```

### All
- Set the Form Request using `$_REQUEST`

```
$form->all()->submit([
    // 
])
```

### Rules
- By default only `DATA TYPE` and `[INPUT_NAME]` is required
    - Always seperate each indicator with a 'colon' `:` or 'pipe' `|`

| DATA TYPE |  INPUT_NAME  | COMPARISON OPERATOR | VALUE TO COMPARE |
|-----------|--------------|---------------------|------------------|
| string    | : country    | :  ==               | : 0              |
| email     | : email      | :                   |                  |

```
$form->rules([
    "string|country|==|0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])
```

- HTML FORM Structure
```
<form>
    <select name="country">
        <option value="0">Select Country</option>
        <option value="NGA">Nigeria</option>
        <option value="USA">United States of America</option>
    </select>

    <input type="hidden" name="csrf_token" value="749c345a1d407f29e777349f5e46a8d6d2cd51454b6719228b5ee28f94c30432">
    <input type="email" name="email" placeholder="Email Address">
</form>
```

### Validate 
- Takes an [optional] `callable` function as the param

```
$form->submit([
    "s:name" => 'Please enter a name',
])->validate(function($response){

    $response->param; //Collection of form data
    $response->getMessage(); //message property
});
```

### Success 
- Expects a `callable` function as the param
    - Message property will be empty string on success `$response->message`

```
$form->submit([
    "s:name" => 'Please enter a name',
])->success(function(){
    //on success
});
```

### Data Flags 
- `Supports 9 Data Flags type`

| Data types | abbr |          Description          |
|------------|------|-------------------------------|
| email      |  e   |  `Email` data validation      |
| bool       |  b   |  `Boolean` data validation    |
| string     |  s   |  `String` data validation     |
| str_len    |  sl  |  `String Length` validation   |
| enum       |  en  |  `Enum` Forms `checkbox \| radio` or any form data that normally has no value when not checked |
| array      |  a   |  `Array` data validation      |
| float      |  f   |  `Float` data validation      |
| int        |  i   |  `Int` data validation        |
| url        |  u   |  `Url` data validation        |


### Operators
- `Supports 10 operational statement`

| sign |          Description          |
|------|-------------------------------|
| ==   |  Equal to                     |
| ===  |  Strictly Equal to            |
| !=   |  Not Equal To                 |
| !==  |  Not Strictly Equal To        |
| >    |  Greater than                 |
| >=   |  Greater than or Equal to     |
| <    |  Less than                    |
| <=   |  Less than or Equal to        |
| <||> |  Less than or Greater than    |
| <&&> |  Less than and Greater than   |


### noInterface
- Expects a `callable` function as the param
    - have access to form data without any validation

```
$form->noInterface(function($response){

    if($response->has('amount')){
        // exec code
    }
});
```

### Before
- Expects a `callable` function as the param
    - Will only execute code within when Request is [GET]
        - CSRF Token `does'nt` apply to this method

```
$form->rules([
    "s:name" => 'Please enter a name',
])->before(function($response){

    // execute code
});
```

### After
- Expects a `callable` function as the param
    - Will always execute no matter the request method type
        - CSRF Token `does'nt` apply to this method

```
$form->after(function(){
    // execute code
});
```

## Reset Error
- Even if you're inside the `success() method`
- With this helper, you can be able to reset the class, to error class

```
->success(function($response){

    $availableUserAmount = 900;

    <!-- Lets say for instance, users have wallet balance and the form request has no error -->
    <!-- But you need to perform another error validator before you allow request to pass through -->
    <!-- Don't forget the add the "return;" key to stop any other code from executing -->


    if($response->amount > $availableUserAmount){
        $response->reset();
        $response->message = "Your wallet balance is too low, Please recharge before you can Subscribe to Plan!";        
        return;
    }

    // perform other request before
});
```

## Only
- Takes a param as an `array` 
    - `keys` of data only needed, from the `form param`

```
->success(function($response){
    //
    $data = $response->only(['password', 'username']);
});
```

## Except
- Exact opposite of `only()` method

```
->success(function($response){
    
    $data = $response->except(['_token']);
});
```

## Has
- Takes a param as `string` input name
    - Returns boolean as `true|\false`

```
->success(function($response){
    
    if($response->has('remeber_me')){
        // execute code
    }
});
```

## Old
- Takes a param as `string` and return old inserted data
    - Second parameter is [optional] `mixed data`. 

```
$form->rules([
    "s:password" => 'Please enter a name',
    "s:retype_pass:!==:{$form->old('password')}" => 'Password mismatch, Please enter same password',
]);
```

- or -- `Helpers Function`
```
<input type="email" name="email" value="<?= old('email', 'default_value')>">
```
![Sample Session Schema](https://raw.githubusercontent.com/tamedevelopers/validator/main/old.png)


## GetForm
- Return all submitted form data as an `array`

```
->success(function($response){

    $data = $response->getForm();
});
```

## Merge
- Same as PHP function `array_merge`
    - Merge two array data together
    - Second data will always repalace any matched key data in the first array

```
->success(function($response){
    
    $data = [
        'name' => 'Lorem Name',
        'user_id' => rand(10000, 99999),
    ];

    $param = $response->merge($data, [
        'password' => md5($param['password'])
    ]);
});
```

## OnlyData
- Return only `data passed` from set of given array elements.

| Keys     |  Data                       |
|----------|-----------------------------|
| `array`  |  Main data to select from   |

```
->success(function($response){

    $data = $response->OnlyData(['email', 'password'], [
        'email'     => 'mailer@mail.com', 
        '_token'    => md5('token'), 
        'age'       => 17,
        'password'  => 'test',
    ]);

    ---
    Only ['email', 'password'] will be returned.
});
```

## ExceptData
- Exact opposite of `OnlyData()` method

| Keys            |  Data                       |
|-----------------|-----------------------------|
| Keys are array  |  Main data to select from   |

```
->success(function($response){
    
    $data = $response->exceptData(['_token'], [
        'email'     => 'mailer@mail.com', 
        '_token'    => md5('token'), 
        'password'  => 'test'
    ]);

    ---
    Return all array element, except ['_token']
});
```

## Get Message and Class
| key     |      Description           |
|---------|----------------------------|
| message | `Message` This convert all error messages and return as a string with `<br>` |
| class   | `Class name` Class name on error and success  |


```
$form->getMessage();
$form->getClass();
```
![Sample Session Schema](https://raw.githubusercontent.com/tamedevelopers/validator/main/getErrorMessage.png)


## Collection
- Forms `param` returns a Collection Class
    - This enable us access property as an `object` or `array index`

```
$form->rules([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])->success(function($response){

    $param = $response->param;


    $param->country;
    $param['country']

    ---
    As you can see, we're able to access data in both ways without errors
})
```

## Collection Methods

|    Methods        |          Description                |
|-------------------|-------------------------------------|
|  toArray()        |  `array` Convert items to array     |
|  toObject()       |  `object` Convert items to object   |
|  toJson()         |  `string` Convert items to json     |


## toObject
- Takes a param as `mixed` data
    - Converts to an `Object` data

```
$form->toObject([
    'food' => 'Basmati Rice'
]);
```

## toArray
- Takes a param as `mixed` data
    - Converts to an `Array` data

```
$form->toArray([
    'food' => 'Basmati Rice'
]);
```

## toJson
- Takes a param as `mixed` data
    - Converts to an `Json` data

```
$form->toJson([
    'food' => 'Basmati Rice'
]);
```

## Helpers

| function  | Description                       |
|-----------|-----------------------------------|
| old()     | Inherit instance of `(new Validator)` old() method    |
| form()    | Return instance of `(new Validator)` class            |


## Useful links

- @author Fredrick Peterson (Tame Developers)
- [Lightweight - PHP Form Validator](https://github.com/tamedevelopers/validator)
- If you love this PHP Library, you can [Buy Tame Developers a coffee](https://www.buymeacoffee.com/tamedevelopers)
- Link to Youtube Video Tutorial on usage will be available soon

