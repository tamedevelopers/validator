# Ultimate Form Validation  - UFV

### @author Fredrick Peterson (Tame Developers)
PHP Ultimate Form Validation Library 

* [Requirements](#requirements)
* [Installation](#installation)
* [Instantiate](#instantiate)
* [Laravel Support](#laravel-support)
* [Usage](#usage)
  * [Instantiate Class Param](#instantiate-class-param)
  * [Submit Method](#submit-method)
  * [Submit Data Type Param](#submit-data-type-param)
  * [Submit Allowed Type Param](#submit-allowed-type-param)
  * [Submit Data Flags](#submit-data-flags)
  * [Data Flags](#data-flags)
  * [Operator Statement](#operator-statement)
  * [Before Submit](#before-submit)
  * [After Submit](#after-submit)
  * [Error Handling](#error-handling)
  * [Success Handling](#success-handling)
* [Get Error Message](#get-error-message)
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
    "peterson/ultimate-validator": "^2.3.0" 
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


## Laravel Support

```
-> Now supports Laravel and with same Functionalities no different

use UltimateValidator\UltimateValidator;


public function save(Request $request){

    $form = new UltimateValidator($request->all());
    or
    $form = new UltimateValidator($_POST);
    or
    $form = new UltimateValidator($_GET);
    or
    $form = new UltimateValidator();
}
```

## USAGE

```
    $form->submit([
        "string:name" => 'Please enter a name',
    ], true)

    or

    $form->submit([
        "string:name" => 'Please enter a name',
    ], false)

    Since it already has a default value, the no need to provide one if you don't intend to discplay all errors at once.
    i.e

    $form->submit([
        "string:name" => 'Please enter a name',
    ])
```

### Instantiate Class Param

**We have two (3) parameter when calling the instantiate the class**

```
-> param |array -- form data. $_GET | $_POST 
-> type |string --  post | get\not required
-> attribute |string|int|array|object|resource\not required
```

** By default if type not passed, then it uses the `$_SERVER['REQUEST_METHOD']` method **
```
$form = new \UltimateValidator\UltimateValidator($_POST, 'POST');
$form = new \UltimateValidator\UltimateValidator($_POST);
$form = new \UltimateValidator\UltimateValidator();
```

### Submit Method 
`->submit()`

```
-> data | array -- input error configuration
-> allowedType |boolean --  true | false (default is set to false)
```

### Submit Data Type Param

- An index Array
```
    the key is a unique modifier.
    the value = Takes a custom message you want user to see as an error
    
    $form->submit([
        "string:name" => 'Please enter a name',
    ])

    Visit (below section to understand key type)
    ## Submit Data Flags
```


### Submit Allowed Type Param

- This method specify how `errors` are to be displayed

| Error |        Description            |
|-------|-------------------------------|
| false |  `Default` This allow errors to be displayed on by one  |
| true  |  This allow all errors to be displayed once, as an array. So you can loop through the array message to show the error|


### Submit Data Flags

`By default only Flags and HTML input name is required`
`Refer to the below Data Flags`

| FLAGS |  HTML_INPUT_NAME | COMPARISON OPERATOR | VALUE TO COMPARE |
|-------|------------------|---------------------|------------------|
| string| : country        | :  ==               | :0               |
| email | : email          | :                   |                  |

### Better example
```
    FLAGS : HTML_INPUT_NAME : OPERATOR : VALUE TO COMPARE
    always seperate each with a `colon` :

    $form->submit([
        "string:country:==:0"   => 'Please Select a Country',
        "email:email"           => 'Please enter a valid email address',
    ])


    HTML FORM Example
    <form>
        <select name="country">
            <option value="0">Select Country</option>
            <option value="NGA">Nigeria</option>
            <option value="USA">United States of America</option>
        </select>

        <input type="email" name="email" placeholder="Email Address">
    </form> 
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


### Before Submit 
- `->beforeSubmit()`

```
** Chainable methods **

-> Expects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
    $form->submit([
        "s:name" => 'Please enter a name',
    ])->beforeSubmit(function($response){

        // execute code
    });

    it can be called anywhere and possition doens't matter

    i.e 2

    $form->beforeSubmit(function($response){

        // execute code
    })->submit([
        "s:name" => 'Please enter a name',
    ]);
```

### After Submit 
`->afterSubmit()`

```
** Chainable methods **

-> Expects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
    $form->submit([
        "s:name" => 'Please enter a name',
    ])->afterSubmit(function(){

        // execute code
    });
```

### Error Handling 
`->error()`

```
** Chainable methods **

-> Expects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
    $form->submit([
        "s:name" => 'Please enter a name',
    ])->error(function($response){

        $response->param; //param property
        $response->message; //message property
    });
```

### Success Handling 
`->success()`

```
** Chainable methods **

-> Expects a callable function as the param
-> pass any variable name of choice to the function, to have access to the class properties
```

```
    $form->submit([
        "s:name" => 'Please enter a name',
    ])->success(function(){
        //on success

        $response->param; //empty param property
    });

    <!-- message property will be empty string on success  -->
    <!-- So you can define and return the message -->
    $response->message; 

    $response->message = "Form has been submitted successfully";
    <!-- If you're not using JSON to return an error, this will be attached to form memory -->
    <!-- you can then use the ->getErrorMessage() method where u need message to be displayed -->
    <!-- Check the `->getErrorMessage()` example section s-->
```

## Get Error Message
`->getErrorMessage()`

```
-> Method that returns the Error Message and Class
-> This can be accesible with the variable that instantiate the UltimateValidator()

-> $form = new UltimateValidator();
```

| key     |      Description           |
|--------------------------------------|
| message | `Message` This convert all error messages and return as a string with `<br>` |
| class   | `Class name` \| on form success  `ULValidate__success` \| on form error  `ULValidate__error` |

```
    $form->getErrorMessage('message');
    $form->getErrorMessage('class');

    If not param is passed to ->getErrorMessage()
    it returns th class by default
```

## Only Method

```
Used to get only the data you need from form elements
Params needs index array of form element keys
```
```
    ->success(function($response){
       
        $data = $response->only(['password', 'username']);
        <!-- This will return only the password and username data -->

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
        <!-- This will return all values except `_token` data -->

        var_dump( $data );
    });
```


## Has Method

```
Used to check if key came along with form
Work just like if isset()
returns boolean\ true|false
```
```
    ->success(function($response){
       
        if($response->has('remeber_me')){

            <!-- execute code -->
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

