<?php

include_once "TameValidator.php";

//supports POST and GET | caseinsensitive
$form = new TameValidator($_GET, 'GET');
$form->submit([
    "string::name" => 'Please enter a name',
    "email::email" => 'Please enter a valid email address',
    "int::age" => 'Age is required',
    ])->error(function($response){
        global $error, $class;

        //for normal error response only just attach message in var used outside
        $class = 'd-block';
        $error = $response->message; 

        echo($response->message);

        //for ajax error return | decode on frontend before usage
        $response->echoJson(1, $response->message);
        
    })->success(function($response){
        //your have access to | $response->param
        echo("Success");

        $response->echoJson(1, "Success");
    });

?>


<!DOCTYPE html>
<head>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<html>
    <body>

        <form method="get" action="<?= $_SERVER["PHP_SELF"];?>" class="form">
            <h2>Form sample</h2>

            <div class="errorMSg <?= @$class ?>">
                <?= @$error ?>
            </div>

            <div class="row">
                <div class="">
                    <label for="html">Name</label>
                    <input type="text" name="name" value="<?= @$_GET['name']?>">
                </div>
                
                <div class="">
                    <label for="html">Email</label>
                    <input type="text" name="email" value="<?= @$_GET['email']?>">
                </div>
                
                <div class="">
                    <label for="html">Age</label>
                    <input type="text" name="age" value="<?= @$_GET['age']?>">
                </div>

                <button type="submit" class="btn mt-2">Submit</button>
            </div>
        </form> 

    </body>
</html>