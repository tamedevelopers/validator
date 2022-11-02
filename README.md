# Ultimate Form Validation  - UFV

### @author Fredrick Peterson (Tame Developers)
PHP Ultimate Form Validation Library 

* [Requirements](#requirements)
* [Installation](#installation)
* [Instantiate](#instantiate)
* [Usage](#usage)
  * [Instantiate Class Param](#instantiate-class-param)
  * [Submit Method](#submit-method)
  * [Data Flags](#data-flags)
  * [Operator Statement](#operator-statement)
  * [Before Submit](#before-submit)
  * [After Submit](#after-submit)
  * [Error Handling](#error-handling)
  * [Success Handling](#success-handling)
* [Laravel Support](#laravel-support)
* [Only Method](#only-method)
* [Except Method](#except-method)
* [Has Method](#has-method)
* [Merge Method](#merge-method)
* [onlyData Method](#onlydata-method)
* [ExceptData Method](#exceptdata-method)
* [GetForm Method](#getForm-method)
* [old Method](#old-method)
* [Useful links](#useful-links)

## Requirements

- `>= php5.3.3+`

## Installation

Prior to installing `ultimate-uploader` get the [Composer](https://getcomposer.org) dependency manager for PHP because it'll simplify installation.

**Step 1** — update your `composer.json`:

```composer.json
"require": {
    "peterson/ultimate-validator": "^3.1"
}
```

**Or composer install**:
```
composer require peterson/ultimate-validator
```

**Step 2** — run [Composer](https://getcomposer.org):

```update
composer update
```


## Instantiate

**Step 1** — Composer  `Instantiate class using`:

```
require_once __DIR__ . '/vendor/autoload.php';

$form = new UltimateValidator\UltimateValidator();

or
require_once __DIR__ . '/vendor/autoload.php';

use \UltimateValidator\UltimateValidator;

$form = new UltimateValidator();
```

**Step 2** — PHP Direct  `Instantiate class using`:

```
include_once "pat_to/UltimateValidator.php";

$form = new UltimateValidator\UltimateValidator();
```


```
**You can download and entire repo and copy the src file alone to directory of your project.**
- src/UltimateValidator.php

```

## Instantiate Class Param

**We have two (3) parameter when calling the instantiate the class**

```
-> param |array -- form data. $_GET | $_POST 
-> type |string --  post | get\not required
-> attribute |string|int|array|object|resource\not reuired
** By default if type not passed, then it's set to 'post' method **
```

```
$form = new \UltimateValidator\UltimateValidator($_POST, 'POST');
$form = new \UltimateValidator\UltimateValidator($_POST);
```

### Laravel Support

```
-> Now supports Laravel and with same Functionalities no different

use UltimateValidator\UltimateValidator;


public function save(Request $request){

    $form = new UltimateValidator($request->all());
    or
    $form = new UltimateValidator($_POST);
    or
    $form = new UltimateValidator($_GET);
}
```

## Submit Method

**->submit() method**

```
-> data |array -- input error configuration
-> allowedType |boolean --  true | false (default is set to false)

** FLAGS|DATA TYPE :  **
** HTML_INPUT_NAME : **
** COMPARISON OPERATOR : **
** VALUE TO COMPARE TO **
```

- data - Data validation handling
```
    FLAGS : HTML_INPUT_NAME : OPERATOR

    $form->submit([
        "string:name" => 'Please enter a name',
        "string:name:<:5" => 'Name should be more than five(5) characters',
        "email:email" => 'Please enter a valid email address',
        "int:age" => 'Age is required',
        "i:age:<:16" => 'Sorry! you must be 16yrs or above to use this site'
    ], false)
```

- allowedType - how to display error
```
    true | false

    True    - will display error message at once, as an array.
    False   - will display error message one by one
```


## Data Flags

- Data Flags type
```
    email   |e
    bool    |b
    string  |s
    array   |a
    float   |f
    int     |i
    url     |u
```


## Operator Statement

- Supports 8 operational statement
```
    =
    ==
    !=
    !==
    >
    >=
    <
    <=
```

## Before Submit

**->beforeSubmit() method**

```
** Chainable methods **

-> espects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
     $form->submit([
        "s:name" => 'Please enter a name',
        "s:name:<:5" => 'Name should be more than five(5) characters',
        "e:email" => 'Please enter a valid email address',
        "i:age" => 'Age is required',
        "i:age:<:16" => 'Sorry! you must be 16yrs or above to use this site',
    ])->beforeSubmit(function(){

        // execute code
    });
```

## After Submit

**->afterSubmit() method**

```
** Chainable methods **

-> espects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
     $form->submit([
        "s:name" => 'Please enter a name',
        "s:name:<:5" => 'Name should be more than five(5) characters',
        "e:email" => 'Please enter a valid email address',
        "i:age" => 'Age is required',
        "i:age:<:16" => 'Sorry! you must be 16yrs or above to use this site',
    ])->afterSubmit(function(){

        // execute code
    });
```

## Error Handling

**->error() method**

```
** Chainable methods **

-> espects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
     $form->submit([
        "s:name" => 'Please enter a name',
        "s:name:<:5" => 'Name should be more than five(5) characters',
        "e:email" => 'Please enter a valid email address',
        "i:age" => 'Age is required',
        "i:age:<:16" => 'Sorry! you must be 16yrs or above to use this site',
    ])->error(function($response){

        $response->param; //param property
        $response->message; //message property
    });
```

## Success Handling

**->success() method**

```
** Chainable methods **

-> espects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
     $form->submit([
        "s:name" => 'Please enter a name',
        "s:name:<:5" => 'Name should be more than five(5) characters',
        "e:email" => 'Please enter a valid email address',
        "i:age" => 'Age is required',
        "i:age:<:16" => 'Sorry! you must be 16yrs or above to use this site',
    ])->error(function($response){

        $response->param; //param property
        $response->message; //message property
    })->success(function(){
        //on success

        $response->param; //param property
        $response->message; //message property
    });
```

## Only Method

```
Used to get only the data you need from form elements
Params needs index array of form element keys
```
```
    ->success(function($response){
       
        $data = $response->only(['password', 'username']);

        var_dump( $data );
    });
```


## Except Method

```
Used to remove form elements you don't need
Very useful for laravel projects
Params needs index array of form element keys
```
```
    ->success(function($response){
       
        $data = $response->except(['_token']);

        var_dump( $data );
    });
```


## Has Method

```
used to check if param is in form array params
Just like if isset()
This check if the key string is in array keys
returns boolean\ true|false
```
```
    ->success(function($response){
       
        if($response->has('remeber_me')){

        }
    });
```


## Merge Method

```
merge array data\Accepts two params of arrays
@param array\ $keys\ Keys are array to be merge
@param array\ $data\ Main data to merge the key with

If the data array has same key_name with the key array. 
Data array value will replace the key array values.

returns array|null
```
```
    ->success(function($response){
       
        $data['user_id'] = rand(10000, 99999);
        $data['password'] = md5($param['password']);

        $param = $response->merge($response->param, $data);

    });
```


## OnlyData Method

```
Used to get only the data you need from array element
@param array\ $keys\ Keys are array
@param array\ $data\ Main data to select from
```
```
    ->success(function($response){
       
        $array = ['email' => 'mailer@mail.com', '_token' => md5('token'), 'password' => 'test'];
        $data = $response->OnlyData(['email', 'password'], $array);

        var_dump( $data );
    });
```


## ExceptData Method

```
Used to exclude the data you don't need from array element
@param array\ $keys\ Keys are array
@param array\ $data\ Main data to select from
```
```
    ->success(function($response){
       
        $array = ['email' => 'mailer@mail.com', '_token' => md5('token'), 'password' => 'test'];
        $data = $response->exceptData(['_token'], $array);

        var_dump( $data );
    });
```


## GetForm Method

```
Can be used anywhere
return all submitted form data in array & object format

```
```
    ->success(function($response){

        $data = $response->getForm();

        var_dump( $data );

        [
            'attribute' => (array) form,
            'attributes' => (object) form
        ];
    });
```

## Old Method

```
Used to output old data entered on form elem
Refer to test/index_text4.php to see sample
```
```
    ->success(function($response){

        var_dump( $response->old('retype_password') );
    });
```

## Useful links

- If you love this PHP Library, you can [Buy Tame Developers a coffee](https://www.buymeacoffee.com/tamedevelopers)
- Link to Youtube Video Tutorial on usage will be available soon

