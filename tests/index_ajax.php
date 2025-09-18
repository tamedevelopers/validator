<?php

include_once __DIR__ . "/include.php";
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

        <form method="post" action="<?= tasset('tests/server.php', false, true);?>" class="form"
            onsubmit="return submitForm(this)">
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
                    <textarea name="message" rows="5" style="resize:none;"
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

                    <label for="terms" style="margin-top: 30px;">
                        Accept terms
                        <input type="checkbox" name="terms" id="terms" 
                            value="accepted" <?= old('terms') ? 'checked' : '' ?> >
                    </label>
                </div>

                <button type="submit" class="btn mt-2">Submit</button>
            </div>
        </form> 


        <script>
            function submitForm(form){
                event.preventDefault();

                let formData = new FormData(form);

                // send via fetch
                fetch(form.action, {
                    method: form.method, // POST
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // parse the JSON response
                    // data = JSON.parse(data);

                    console.log(
                        data,
                        // data.response,
                        // data.message
                    );
                    return false;
                    // handle success/error response from server
                    if(data.status === "success"){
                        alert("Form submitted successfully ✅");
                        
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                });
            }
        </script>
    </body>
</html>