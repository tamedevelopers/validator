<?php

include_once __DIR__ . "/include.php";

    
$result = $form->token(true)->error(false)->rules([
    "string|name"       => 'Please enter a name',
    "str_len|name|<|5"  => 'Name should be more than five(5) characters',
    "email|email"       => 'Please enter a valid email address',
    "int:age"           => 'Age is required',
    "int:age:<:16"      => 'Sorry! you must be 16yrs and above to use this site',
    "float:amount"      => 'Enter Loan Amount',
    "array:activities"  => 'Select one or more activities',
    "array:activities"  => 'Select one or more activities',
    "string:message"    => 'Message cannot be empty',
    "enum:terms"        => 'Accept terms and condition',
])->validate(function($response){

    return $response->json(1, $response->message);
})->save(function($response){
    // access the form data
    $param = $response->param;
    
    // access parent scope data\ $data
    $attribute = $response->attribute;

    // dump(
    //     // $param->message,
    //     $param->toArray(),
    // );

    return $response->json(0, "Submitted Successfully");
});

