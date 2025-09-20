<?php


    include_once __DIR__ . "/../vendor/autoload.php";

    $data = [
        'user'              => 'F. Pete', 
        'marital_status'    => 'Single', 
        'occupation'        => 'Web Artisans'
    ];
    
    config_form(
        csrf_token: true,
        request: 'all',
    );

    $form = form($data);
    $form->rules([
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
    ])->save(function($response){
        // access the form data
        $param = $response->param;
        
        // access parent scope data\ $data
        $attribute = $response->getAttribute();

        // message
        $response->message = "Submitted Successfully";

        dump(
            // $param->message,
            $param->toArray(),
            // $param->email,
            // $param['name'],
            // $attribute->occupation,
            // $param->activities
        );
        
        // var_dump( $response->getForm() );
    });
    
?>


<!DOCTYPE html>
<head>
    <title>Form validation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1">
    <link href="include/style.css" rel="stylesheet" type="text/css">
</head>
<html>
    <body>

        <form method="get" action="<?= $_SERVER["PHP_SELF"];?>" class="form">
            <h2>Form sample</h2>
            
            <div class="errorMsg mb-5 <?= $form->getClass() ?>">
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
                    <input type="text" name="email" value="<?= old('email'); ?>">
                </div>
                
                <div class="">
                    <label for="html">Age</label>
                    <input type="number" name="age" value="<?= old('age'); ?>">
                </div>
                
                <div class="">
                    <label for="html">Loan Amount</label>
                    <input type="number" step="any" name="amount" value="<?= old('amount'); ?>">
                </div>
                
                <div class="">
                    <label for="html">Message</label>
                    <textarea name="message" rows="7" 
                        cols="81"><?= old('message'); ?></textarea>
                </div>

                <div class="activities">
                    <p class="title">
                        Activities you're interested in:
                    </p>

                    <label for="reading">
                        Reading
                        <input type="checkbox" name="activities[]" value="reading" id="reading" <?= old('activities.reading') ? 'checked' : '' ?> >
                    </label>
                    <label for="writing">
                        Writing
                        <input type="checkbox" name="activities[]" value="writing" id="writing" <?= old('activities.writing') ? 'checked' : '' ?>>
                    </label>
                    <label for="running">
                        Running
                        <input type="checkbox" name="activities[]" value="running" id="running" <?= old('activities.running') ? 'checked' : '' ?>>
                    </label>
                    <label for="swimming">
                        Swimming
                        <input type="checkbox" name="activities[]" value="swimming" id="swimming" <?= old('activities.swimming') ? 'checked' : '' ?>>
                    </label>

                    <label for="terms" style="margin-top: 30px;">
                        Accept terms
                        <input type="checkbox" name="terms" id="terms" 
                            value="accepted" <?= old('terms') ? 'checked' : '' ?> >
                    </label>
                </div>

                <button type="submit" class="btn mt-2">Submit</button>
            </div>
        </form> 

    </body>
</html>