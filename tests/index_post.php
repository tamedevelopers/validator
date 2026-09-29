<?php

    use Tamedevelopers\Validator\Validator;

    include_once __DIR__ . "/../vendor/autoload.php";

    // it use $_SERVER['REQUEST_METHOD'] as default if not passed to the handler
    $form = new \Tamedevelopers\Validator\Validator();

    $form->token(false)->rules([
        "s:name"            => 'Please enter a name',
        "sl:name:<:5"       => 'Name should be more than five(5) characters',
        "e:email"           => 'Please enter a valid email address',
        "float:age"         => 'Age is required',
        "i:age:<:16"        => 'Sorry! you must be 16yrs and above to use this site',
        "i:age:>:36"        => 'Age limit must be less than 36yrs to use this site',
        "dev:description"  => 'Description is required',
    ])->save(function(Validator $response){
        // access the form data
        $param = $response->except(['_token']);

        // message
        $response->message = "Submitted Successfully";

        dump(
            // $param,
            $response->getForm('name'),
            $response->param('email'),
            // $response->toObject()
        );
    });

    // dd(
    //     $form->has('description'),
    //     // $form->param('description'),
    //     // $form->old('description')
    // );

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

        <form method="post" action="<?= $_SERVER["PHP_SELF"];?>" class="form">
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
                    <input type="text" name="email" value="<?= $form->old('email'); ?>">
                </div>
                
                <div class="">
                    <label for="html">Age</label>
                    <input type="number" name="age" value="<?= $form->old('age'); ?>">
                </div>

                <label for="reading">
                    Reading
                    <input type="checkbox" name="activities[]" 
                        value="reading" id="reading" <?= old('activities.reading') ? 'checked' : '' ?> >
                </label>
                <label for="writing">
                    Writing
                    <input type="checkbox" name="activities[]" 
                        value="writing" id="writing" <?= old('activities.writing') ? 'checked' : '' ?>>
                </label>
                
                <div class="">
                    <label for="html">Description</label>
                    <textarea name="description" 
                        rows="6"
                        style="resize: none; width: 100%;"><?= $form->old('description'); ?></textarea>
                </div>

                <button type="submit" class="btn mt-2">Submit</button>
            </div>
        </form> 

    </body>
</html>