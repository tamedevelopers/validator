# PHP Form Validator  - PFV

[![Total Downloads](https://poser.pugx.org/peterson/validator/downloads)](https://packagist.org/packages/peterson/validator)
[![Latest Stable Version](https://poser.pugx.org/peterson/validator/version.png)](https://packagist.org/packages/peterson/validator)
[![License](https://poser.pugx.org/peterson/validator/license)](https://packagist.org/packages/peterson/validator)
[![Build Status](https://github.com/tamedevelopers/phpFormValidator/actions/workflows/php.yml/badge.svg)](https://github.com/tamedevelopers/phpFormValidator/actions)
[![Code Coverage](https://codecov.io/gh/peterson/validator/branch/2.2.x/graph/badge.svg)](https://codecov.io/gh/peterson/validator/branch/3.2.2.x)

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
  * [Submit](#submit)
  * [Error](#error)
  * [Success](#success)
  * [Data Flags](#data-flags)
  * [Operator Statement](#operator-statement)
  * [noInterface](#nointerface)
  * [Before Submit](#before-submit)
  * [After Submit](#after-submit)
* [ResetError](#resetError)
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
* [Form Request](#form-request)
* [toObject](#toobject)
* [toArray](#toarray)
* [toJson](#tojson)
* [Helpers](#helpers)
* [Useful links](#useful-links)

## Requirements

- `>= php7.2+`

## Installation

Prior to installing `ultimate-uploader` get the [Composer](https://getcomposer.org) dependency manager for PHP because it'll simplify installation.

**Step 1** — update your `composer.json`:
```composer.json
"require": {
    "tamedevelopers/validator": "^4.2.0" 
}
```

**Step 2** — run [Composer](https://getcomposer.org):
```update
composer update
```

**Or composer require**:
```
composer require tamedevelopers/validator
```

## Instantiate
- Optional `attribute` param. Whatever data outside class you need to use inside.
    - Will automatically be converted to `Collection Data`

**Step 1** — `Instantiate class using`:

```
require_once __DIR__ . '/vendor/autoload.php';

use \Tamedevelopers\Validator\Validator;

$form = new Validator();
```

- **Example 2**
```
$data = [
    'user' => 'F. Pete', 
    'marital' => 'Single',
];

$form = new Tamedevelopers\Validator\Validator($data);
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

| Methods   |        Description            |
|-----------|-------------------------------|
| ->et()    |  Takes param as `bool` to format error's on display: `single or multiple` |
| ->token() |  Takes param as `bool` to Enable or Disable `csrf_token` for each request |
| ->post()  |  No param needed. SET `form` request to `POST` only |
| ->get()   |  No param needed. SET `form` request to `GET` only |

```
$form->post()->submit([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
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
        'error'     => 'form__error',
        'success'   => 'form__success'
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
![Sample Csrf Form Input](https://raw.githubusercontent.com/tamedevelopers/phpFormValidator/main/getErrorMessage.png)

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
$form->et(false);
```

- or

```
$form->et(true)->submit([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])
```

### Token
- Takes a param as `bool` Default is `false`
    - Allow disability of `csrf_token` on each form request

| Error |        Description            |
|-------|-------------------------------|
| false |  `Default` Will disable `csrf_token` usage  |
| true  |  This allow `csrf_token` per request only   |

```
$form->token(false);
```

- or

```
$form->token(true)->submit([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])
```

### POST
- You can call separately or Call Before any other method, if intend to use.
    - This will set the Form Request to `POST`
        - This will always override the `config_form()` settings

```
$form->post()->submit([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])
```

### GET
- Same as `POST`
    - This will set the Form Request to `GET`

```
$form->get()->submit([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])
```

### All
- Same as `POST\|GET`
    - This will set the Form Request using `$_SERVER['REQUEST_METHOD']`

```
$form->all()->submit([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])
```

### Submit
- By default only Flags and HTML input name is required
    - Always seperate each indicator with a `colon` `:`

| FLAGS  |  HTML_INPUT_NAME | COMPARISON OPERATOR | VALUE TO COMPARE |
|--------|------------------|---------------------|------------------|
| string | : country        | :  ==               | : 0              |
| email  | : email          | :                   |                  |

```
$form->submit([
    "string:country:==:0"   => 'Please Select a Country',
    "email:email"           => 'Please enter a valid email address',
])
```

- HTML FORM Structure Sample
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

### Error 
- Expects a `callable` function as the param

```
$form->submit([
    "s:name" => 'Please enter a name',
])->error(function($response){

    $response->param; //Collection of form data
    $response->message; //message property
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

    $response->param; //Collection of form data
});
```

### Data Flags 
- `Supports 9 Data Flags type`

| flag_name  | abbr |          Description          |
|------------|------|-------------------------------|
| email      |  e   |  `Email` data validation      |
| bool       |  b   |  `Boolean` data validation    |
| string     |  s   |  `String` data validation     |
| str_len    |  sl  |  `String Length` validation   |
| enum       |  en  |  `Enum` Forms checkbox, radio or any form data that normally has no value when not checked |
| array      |  a   |  `Array` data validation      |
| float      |  f   |  `Float` data validation      |
| int        |  i   |  `Int` data validation        |
| url        |  u   |  `Url` data validation        |


### Operator Statement 
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
    - Since `Submit` method must be passed for you to access most properties
        - Wrapping Non-Submitable code inside the `noInterface` makes code more neat

```
$form->noInterface(function($response){

    if($response->has('amount')){
        // exec code
    }
});
```

### Before Submit 
- Expects a `callable` function as the param
    - Pass any variable name of choice to the function, to have access instance of class

```
$form->submit([
    "s:name" => 'Please enter a name',
])->beforeSubmit(function($response){

    // execute code
});
```
- Positioning doen't matter, as they're `Chainable methods`

```
$form->beforeSubmit(function($response){

    // execute code
})->submit([
    "s:name" => 'Please enter a name',
]);
```

### After Submit 
- Same as `beforeSubmit()`

```
$form->submit([
    "s:name" => 'Please enter a name',
])->afterSubmit(function(){

    // execute code
});
```

## ResetError
- Even if you're inside the `success() method`
- With this helper, you can be able to reset the class, to error class

```
->success(function($response){

    $availableUserAmount = 900;

    <!-- Lets say for instance, users have wallet balance and the form request has no error -->
    <!-- But you need to perform another error validator before you allow request to pass through -->
    <!-- Don't forget the add the "return;" key to stop any other code from executing -->


    if($response->amount > $availableUserAmount){
        $response->resetError();
        $response->message = "Your wallet balance is too low, Please recharge before you can Subscribe to Plan!";        
        return;
    }

    // perform other request before
});
```

## Only
- Takes a param as an `array` 
    - `keys` of data only needed

```
->success(function($response){

    $data = $response->only(['password', 'username']);

    ---
    Will return only the password and username
});
```

## Except
- Takes a param as an `array` 
    - `keys` of data only needed

```
->success(function($response){
    
    $data = $response->except(['_token']);

    ---
    Will return all values except `_token`
});
```

## Has
- Takes a param as `string` 
    - Return `bool` true|false

```
->success(function($response){
    
    if($response->has('remeber_me')){

        // execute code
    }

    --
    If the remeber_me exist along with submitted form data
});
```

## Old
- Takes a param as `string`
    - Second parameter is optional `mixed data`
    - Return old inserted data

```
$form->submit([
    "s:password" => 'Please enter a name',
    "s:retype_pass:!==:{$form->old('password')}" => 'Password mismatch, Please enter same password',
]);


<input type="email" name="email" placeholder="Email Address" value="<?= $form->old('email')>">
```

- or -- `Helpers Function`
```
<input type="email" name="email" value="<?= old('email', 'default_value')>">
```
![Sample Session Schema](https://raw.githubusercontent.com/tamedevelopers/phpFormValidator/main/old.png)



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
- Used to get only the data you need from array element

| Keys            |  Data                       |
|-----------------|-----------------------------|
| Keys are array  |  Main data to select from   |

```
->success(function($response){
    $array = [
        'email'     => 'mailer@mail.com', 
        '_token'    => md5('token'), 
        'password'  => 'test'
    ];

    $data = $response->OnlyData(['email', 'password'], $array);

    ---
    This will select only the `email` and `password` from $array
});
```

## ExceptData
- Used to exempt data you dont need from array element

| Keys            |  Data                       |
|-----------------|-----------------------------|
| Keys are array  |  Main data to select from   |

```
->success(function($response){
    
    $array = [
        'email'     => 'mailer@mail.com', 
        '_token'    => md5('token'), 
        'password'  => 'test'
    ];

    $data = $response->exceptData(['_token'], $array);

    ---
    Will return all values except `_token`
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
![Sample Session Schema](https://raw.githubusercontent.com/tamedevelopers/phpFormValidator/main/getErrorMessage.png)


## Collection
- Forms `param` returns a Collection Class
    - This enable us access property as an `object` or `array index`

```
$form->submit([
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


## Form Request
- Optional\ Takes a param as `string` on each method to get needed data
    - Returns `data` or `null`

| function              | Description                   |
|-----------------------|-------------------------------|
| form_request()->all()      | Return All requests data      |
| form_request()->get()      | Return GET data               |
| form_request()->post()     | Return POST data              |
| form_request()->cookie()   | Return Cookies data           |
| form_request()->session()  | Return Session data           |
| form_request()->env()      | Return ENV data               |

```
form_request()->get('password')
form_request()->env('APP_DEBUG')
```

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

| function      | Description                       |
|---------------|-----------------------------------|
| old()         | Inherit instance of `(new Validator)` old() method    |
| form()        | Return instance of `(new Validator)` class            |
| form_request()| Return instance of `(new RequestMethod)` class        |


## Useful links

- @author Fredrick Peterson (Tame Developers)
- [Lightweight - PHP Form Validator](https://github.com/tamedevelopers/phpFormValidator)
- If you love this PHP Library, you can [Buy Tame Developers a coffee](https://www.buymeacoffee.com/tamedevelopers)
- Link to Youtube Video Tutorial on usage will be available soon

