# PHP Form Validator  - PFV

[![Total Downloads](https://poser.pugx.org/peterson/php-form-database/downloads)](https://packagist.org/packages/peterson/php-form-database)
[![Latest Stable Version](https://poser.pugx.org/peterson/php-form-database/version.png)](https://packagist.org/packages/peterson/php-form-validator)
[![License](https://poser.pugx.org/peterson/php-form-database/license)](https://packagist.org/packages/peterson/php-form-database)
[![Build Status](https://github.com/tamedevelopers/phpFormValidator/actions/workflows/php.yml/badge.svg)](https://github.com/tamedevelopers/phpFormValidator/actions)
[![Code Coverage](https://codecov.io/gh/peterson/php-form-database/branch/2.2.x/graph/badge.svg)](https://codecov.io/gh/peterson/php-form-database/branch/3.2.2.x)

## Documentation

* [Requirements](#requirements)
* [Installation](#installation)
* [Instantiate](#instantiate)
* [Laravel Support](#laravel-support)
* [Usage](#usage)
  * [Form Error Type](#form-error-type)
  * [Submit](#submit)
  * [Error](#error)
  * [Success](#success)
  * [Data Flags](#data-flags)
  * [Operator Statement](#operator-statement)
  * [noInterface](#nointerface)
  * [Before Submit](#before-submit)
  * [After Submit](#after-submit)
* [Only](#only)
* [Except](#except)
* [Has](#has)
* [Old](#old)
* [Merge](#merge)
* [onlyData](#onlydata-method)
* [ExceptData](#exceptdata-method)
* [GetForm](#getForm)
* [Collection](#collection)
* [Get Error Message](#get-error-message)
* [Collection Methods](#collection-methods)
* [Request](#request)
* [toObject](#toobject)
* [toArray](#toarray)
* [toJson](#tojson)
* [Helpers](#helpers)
* [Useful links](#useful-links)

## Requirements

- `>= php5.3.3+`

## Installation

Prior to installing `ultimate-uploader` get the [Composer](https://getcomposer.org) dependency manager for PHP because it'll simplify installation.

**Step 1** — update your `composer.json`:
```composer.json
"require": {
    "peterson/php-form-validator": "^3.2.2" 
}
```

**Step 2** — run [Composer](https://getcomposer.org):
```update
composer update
```

## Instantiate
- Optional `attribute` param. Whatever data outside class you need to use inside.
    - Will automatically be converted to `Collection Data`

**Step 1** — Composer  `Instantiate class using`:

```
require_once __DIR__ . '/vendor/autoload.php';

use \UltimateValidator\UltimateValidator;

$form = new UltimateValidator();
```

- **Example 2**
```
$data = [
    'user' => 'F. Pete', 
    'marital' => 'Single',
];

$form = new UltimateValidator\UltimateValidator($data);
```

- **Example 3** `Helpers Function`
```
$form = opForm();
```

## Laravel Support
- Now supports Laravel and with same Functionalities no different
    - `use UltimateValidator\UltimateValidator;`

```
public function save(Request $request){

    $form = new UltimateValidator();
    or
    $form = opForm();
}
```

## USAGE
- All Methods of usage 


### Form Error Type
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
    - Return old inserted data

```
$form->submit([
    "s:password" => 'Please enter a name',
    "s:retype_pass:!==:{$form->old('password')}" => 'Password mismatch, Please enter same password',
]);
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

## Get Error Message
| key     |      Description           |
|---------|----------------------------|
| message | `Message` This convert all error messages and return as a string with `<br>` |
| class   | `Class name` \| on form success  `opForm__success` \| on form error  `opForm__error` |

- Takes a param as `string` message|class
    - If no param is passed, it returns `class name`

```
$form->getErrorMessage('message');
$form->getErrorMessage('class');
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


## Request
- Optional\ Takes a param as `string` on each method to get needed data
    - Returns `data` or `null`

| function              | Description                   |
|-----------------------|-------------------------------|
| request()->all()      | Return All requests data      |
| request()->get()      | Return GET data               |
| request()->post()     | Return POST data              |
| request()->cookie()   | Return Cookies data           |
| request()->session()  | Return Session data           |
| request()->env()      | Return ENV data               |

```
request()->get('password')
request()->env('APP_DEBUG')
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
| opForm()      | Return instance of `(new UltimateValidator)` class  |
| request()     | Return instance of `(new RequestMethod)` class      |


## Useful links

- @author Fredrick Peterson (Tame Developers)
- [Lightweight - PHP Form Validator](https://github.com/tamedevelopers/phpFormValidator)
- If you love this PHP Library, you can [Buy Tame Developers a coffee](https://www.buymeacoffee.com/tamedevelopers)
- Link to Youtube Video Tutorial on usage will be available soon

