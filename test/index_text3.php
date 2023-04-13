<?php

    include_once __DIR__ . "/../vendor/autoload.php";

    //auto find request method if no param set
    $form = new \UltimateValidator\UltimateValidator();
    $form->submit([
        "string:current_password"   => 'Enter your old password',
        "string:new_password"       => 'Enter a new password',
        "string:retype_password"    => 'Retype new password',
        "string:retype_password:!==:{$form->old('new_password')}" => 'Password mis-match... Try again.' 
    ], true)->error(function($response){ 
        
    })->success(function($response){
        //your have access to | $response->param
        $param = $response->param;
        $response->flash = "d-block success";

        // this will remove any key value in object param 
        $param = $response->except(['retype_password', '__token']);

        // this will return only the key value in object param 
        // $param = $response->only(['retype_password', 'current_password']);

        // returns boolean true|false in object param 
        $has = $response->has('retype_password');

        // new data
        $merge = $response->merge([
            'first_name' => 'Tame',
            'last_name' => 'Developers',
            'current_password' => 'Developers',
        ], $param);

        var_dump($merge);
        var_dump($param);
        var_dump($has);
        var_dump($response);
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
            
            <div class="errorMSg mb-5 <?= $form->getErrorMessage('class') ?>">
                <?= $form->getErrorMessage('message') ?>
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