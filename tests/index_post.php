<?php

    include_once __DIR__ . "/../vendor/autoload.php";

    // it use $_SERVER['REQUEST_METHOD'] as default if not passed to the handler
    $form = new \Tamedevelopers\Validator\Validator();

    $form->token(false)->rules([
        "s:name"        => 'Please enter a name',
        "sl:name:<:5"   => 'Name should be more than five(5) characters',
        "e:email"       => 'Please enter a valid email address',
        "float:age"       => 'Age is required',
        // "i:age:<:16"    => 'Sorry! you must be 16yrs and above to use this site',
        // "i:age:>:36"    => 'Sorry! Age limit is 36 in other to use this site',
    ])->save(function($response){
        // access the form data
        $param = $response->except(['_token']);

        // message
        $response->message = "Submitted Successfully";

        dump(
            $param,
            $response->param
        );
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

        <form method="post" action="<?= $_SERVER["PHP_SELF"];?>" class="form">
            <h2>Form sample</h2>

            <div class="errorMSg mb-5 <?= $form->getClass() ?>">
                <?= $form->getMessage() ?>
            </div>

            <?php csrf() ?>

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