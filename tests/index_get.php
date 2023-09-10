<?php

    include_once __DIR__ . "/../vendor/autoload.php";

    $data = [
        'user'              => 'F. Pete', 
        'marital_status'    => 'Single', 
        'occupation'        => 'Web Artisans'
    ];
    

    $form = form($data);
    $form->et(true)->get()->submit([
        "string:name"       => 'Please enter a name',
        "str_len:name:<:5"  => 'Name should be more than five(5) characters',
        "email:email"       => 'Please enter a valid email address',
        "int:age"           => 'Age is required',
        "int:age:<:16"      => 'Sorry! you must be 16yrs and above to use this site',
    ])->error(function($response){ 

        //for ajax error return | decode on frontend before usage
        $response->echoJson(0, $response->message);

    })->success(function($response){
        // access the form data
        $param = $response->param;
        
        // access parent scope data\ $data
        $attribute = $response->attribute;

        // message
        $response->message = "Submitted Successfully";

        var_dump($param->email);
        var_dump($param['name']);
        var_dump($attribute->occupation);
        // var_dump( $response->getForm() );
    });

?>


<!DOCTYPE html>
<head>
    <title>Form validation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1">
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<html>
    <body>

        <form method="get" action="<?= $_SERVER["PHP_SELF"];?>" class="form">
            <h2>Form sample</h2>
            
            <div class="errorMSg mb-5 <?= $form->getClass() ?>">
                <?= $form->getMessage() ?>
            </div>

            <?= csrf() ?>

            <div class="row">
                <div class="">
                    <label for="html">Name</label>
                    <input type="text" name="name" value="<?= $form->old('name'); ?>">
                </div>
                
                <div class="">
                    <label for="html">Email</label>
                    <input type="text" name="email" value="<?= $form->old('email'); ?>">
                </div>
                
                <div class="">
                    <label for="html">Age</label>
                    <input type="text" name="age" value="<?= $form->old('age'); ?>">
                </div>

                <button type="submit" class="btn mt-2">Submit</button>
            </div>
        </form> 

    </body>
</html>