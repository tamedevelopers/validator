<?php

    include_once __DIR__ . "/../vendor/autoload.php";

    $outsideParam = [
        'first_name' => 'Tame',
        'last_name' => 'Developers',
        'age' => 0,
    ];

    //auto find request method if no param set
    $form = new \UltimateValidator\UltimateValidator($_REQUEST, 'GET', $outsideParam);
    $form->submit([
        "string:current_password"   => 'Enter your old password',
        "string:new_password"       => 'Enter a new password',
        "string:retype_password"    => 'Retype new password',
        "string:retype_password:!==:{$form->old('new_password')}" => 'Password mis-match... Try again.' 
    ], true)->beforeSubmit(function(){

        var_dump( 'beforeSubmit example usage'  );
    })->afterSubmit(function(){

        var_dump( 'afterSubmit usage example'  );
    })
    ->error(function($response){ 

        // $response->echoJson(0, $response->message);
    })->success(function($response) use ($outsideParam){
        //your have access to | $response->param
        $param = $response->param;

        // param in object format
        $params = $response->params;

        // now you can use the outside param anywhere inside the method scope
        $attributeParam = $response->attribute;

        // the use param name
        $use = $outsideParam;

        $data['user_id'] = rand(10000, 99999);
        $data['retype_password'] = md5($param['retype_password']);

        // merge example
        $merge = $response->merge($response->param, $data);

        // get form
        $form = $response->getForm();

        // message
        $response->message = "Form submitted Successfully";

        var_dump( $form );
        var_dump($param);
        // var_dump($attributeParam );
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