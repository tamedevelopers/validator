<?php

    include_once "../src/UltimateValidator.php";

    $outsideParam = [
        'first_name' => 'Tame',
        'last_name' => 'Developers',
        'age' => 0,
    ];

    //auto find request method if no param set
    $form = new \UltimateValidator\UltimateValidator($_REQUEST, 'GET', $outsideParam);
    $form->submit([
        "string:current_password" => 'Enter your old password',
        "string:new_password" => 'Enter a new password',
        "string:retype_password" => 'Retype new password',
        "string:retype_password:!==:{$form->old('new_password')}" => 'Password mis-match... Try again.' 
    ], true)->error(function($response){ 

        $response->flash = "d-block danger";

        $response->echoJson(0, $response->message);
    })->success(function($response){
        //your have access to | $response->param
        $param = $response->param;
        $response->flash = "d-block success";

        // now you can use the outside param anywhere inside the method scope
        // ->attribute
        $outsideParam = $response->attribute;

        var_dump($param);
        var_dump($outsideParam );
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
            <div class="errorMSg mb-5 <?= $form->flash ?>">
                <?php 
                    if(is_array($form->message)){
                        foreach($form->message as $value){ echo "{$value} <br>"; }
                    }
                    else{ echo $form->message;}
                ?>
            </div>

            <div class="row">
                <div class="">
                    <label for="html">Current password</label>
                    <input type="text" name="current_password" value="<?= $form->old('current_password'); ?>">
                </div>
                
                <div class="">
                    <label for="html">New password</label>
                    <input type="text" name="new_password" value="<?= $form->old('new_password'); ?>">
                </div>
                
                <div class="">
                    <label for="html">Retype password</label>
                    <input type="text" name="retype_password" value="<?= $form->old('retype_password'); ?>">
                </div>

                <button type="submit" class="btn mt-2">Submit</button>
            </div>
        </form> 

    </body>
</html>