# Ultimate Form Validation  - UFV

### @author Fredrick Peterson (Tame Developers)
PHP Ultimate Form Validation Library 

* [Requirements](#requirements)
* [Installation](#installation)
* [Instantiate](#instantiate)
* [Usage](#usage)
  * [Instantiate Class Param](#instantiate-class-param)
  * [SUBMIT METHOD](#submit-method)
  * [DATA FLAGS](#data-flags)
  * [OPERATOR STATEMENT](#operator-statement)
  * [ERROR HANDLING](#error-handling)
  * [SUCCESS HANDLING](#success-handling)
* [Useful links](#useful-links)

## Requirements

- `>= php5.3.3+`

## Installation

Prior to installing `ultimate-uploader` get the [Composer](https://getcomposer.org) dependency manager for PHP because it'll simplify installation.

**Step 1** — update your `composer.json`:

```composer.json
"require": {
    "peterson/ultimate-validator": "^1.0"
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

**We have two (2) parameter when calling the instantiate the class**

```
-> param |array -- form data. $_GET | $_POST 
-> type |string --  post | get
** By default if type not passed, then it's set to 'post' method **
```

```
$form = new \UltimateValidator\UltimateValidator($_POST, 'POST');
```


## SUBMIT METHOD

**->submit() method**

```
-> data |array -- input error configuration
-> allowedType |boolean --  true | false (default is set to false)
-> beforeIssetFunc |callable --  before form is set
-> afterIssetFunc |callable --  after form is set

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

- beforeIssetFunc  
```
    function call | execute codes before form is set
```

- afterIssetFunc 
```
    function call | execute codes after form is set
```


## DATA FLAGS

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


## OPERATOR STATEMENT

- Supports 6 operational statement
```
    ==
    ===
    !=
    !==
    >
    <
```

## ERROR HANDLING

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

## SUCCESS HANDLING

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



### Useful links

- If you love this PHP Library, you can [Buy Tame Developers a coffee](https://www.buymeacoffee.com/tamedevelopers)
- Link to Youtube Video Tutorial on usage will be available soon

