<?php

    include_once "../src/UltimateValidator.php";

    //error handler storage
    $ERROR_HANDLER = ['msg' => '', 'class' => ''];

    //supports POST and GET | caseinsensitive
    $form = new \UltimateValidator\UltimateValidator($_GET, 'GET');

    $form->submit([
        "string:name" => 'Please enter a name',
        "s:name:<5" => 'Name should be more than five(5) characters',
        "email:email" => 'Please enter a valid email address',
        "int:age" => 'Age is required',
        "i:age:<16" => 'Sorry! you must be 16yrs or above to use this site',
    ], true)->error(function($response){

        //for normal error response only just attach message in var used outside
        $GLOBALS['ERROR_HANDLER']['msg'] = $response->message;
        $GLOBALS['ERROR_HANDLER']['class'] = 'd-block danger';

        //for ajax error return | decode on frontend before usage
        $response->echoJson(0, $response->message);
        
    })->success(function($response){
        //your have access to | $response->param
        $param = $response->param;


        $GLOBALS['ERROR_HANDLER']['msg'] = 'Success';
        $GLOBALS['ERROR_HANDLER']['class'] = 'd-block success';

        //
        var_dump($param);
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
            <div class="errorMSg mb-5 <?= $ERROR_HANDLER['class'] ?>">
                <?php 
                    if(is_array($ERROR_HANDLER['msg'])){
                        foreach($ERROR_HANDLER['msg'] as $value){ echo "{$value} <br>"; }
                    }
                    else{ echo $ERROR_HANDLER['msg'];}
                ?>
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