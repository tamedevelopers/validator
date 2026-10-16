<?php

use Tamedevelopers\File\File;
use Tamedevelopers\Validator\Validator;

    include_once __DIR__ . "/../vendor/autoload.php";

    // it use $_SERVER['REQUEST_METHOD'] as default if not passed to the handler
    $form = new \Tamedevelopers\Validator\Validator();
    $attachment = File::name('attachment')
        ->size('1mb')
        ->limit(3)
        ->label('Attachment')
        ->width('200px', false)
        ->mime('general_media');


    $form->token(true)->rules([
        "s:name"            => 'Please enter a name',
        "sl:name:<:5"       => 'Name should be more than five(5) characters',
        "e:email"           => 'Please enter a valid email address',
        "float:age"         => 'Age is required',
        "file:avatar"       => 'size:500kb|limit:1|generate:false|mime:pdf|width:200, false|required:false',
        "file:attachment"   => $attachment,
        "i:age:<:16"        => 'Sorry! you must be 16yrs and above to use this site',
        "i:age:>:36"        => 'Age limit must not be more than 36yrs to use this site',
        "dev:description"   => 'Description is required',
    ])->save(function(Validator $response){
        // access the form data
        $param = $response->except(['_token']);

        // message
        $response->message = "Submitted Successfully";

        // save validated file
        // $response->file('attachment')->save();
        
        dump(
            $response,
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

        <form method="post" action="<?= $_SERVER["PHP_SELF"];?>" class="form" enctype="multipart/form-data">
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

                <!--file upload-->
                <div class="col-sm-12 mt-3" style="margin: 10px 0 30px;">
                    <div class="form-group">
                        <label for="upload" style="display: block; margin-bottom: 10px;">
                            Image Avatar
                        </label>
                        <input type="file" class="form-control-file" name="avatar">
                    </div>
                </div>

                <!--file attachment-->
                <div class="col-sm-12 mt-3" style="margin: 10px 0 30px;">
                    <div class="form-group">
                        <label for="upload" style="display: block; margin-bottom: 10px;">
                            File Attachment
                            <span style="font-size: 12px; display: block; margin-top: 5px;">
                                <?= $attachment->getValidationMessage() ?>
                            </span>
                        </label>
                        <input type="file" class="form-control-file" name="attachment[]" multiple>
                    </div>
                </div>

                <div>
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
                </div>
                
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