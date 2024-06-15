<?php

require __DIR__ . '/config.inc.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.inc.php';

$cli_green = "\033[1;92m";
$cli_red = "\033[1;91m";
$cli_default = "\033[1;39m";

$i = 0;

while (time() < NOTIFY_UNTILL) {
    
    $time_left_in_secs = NOTIFY_UNTILL - time();   
    $time_left_in_mins = $time_left_in_secs / 60;   
    $time_left_in_hours = $time_left_in_mins / 60;   
    $time_left_in_days = $time_left_in_hours / 24;   

    foreach (getRecipients(RECIPIENTS_FILE) as $j => $recipient) {
        $i++;
        $name = $recipient['name'];
        $email = $recipient['email'];

        $subject = "পড় ভাই পড় 💥!!! আর মাত্র " . en2bn(round($time_left_in_hours)) . ' ঘণ্টা বাকি!!!';

        $body = <<<MSGBODY
    
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$subject}</title>
            </head>
            <body>
                প্রিয় {$name}, <br>

                <h2 style="color:red;">অনুগ্রহ করে পড়তে বসুন</h2>

                <br>
                <hr>
                <br>

                <center>আপনার শুভাকাঙ্ক্ষী - <a href="https://sadiq.us.to">সাদিক</a></center>

            </body>
            </html>
    
        MSGBODY;

        if (sendMail($email, $subject, $body)) {
            echo $cli_green . $i . '. Mail Sent Successfully' . PHP_EOL . $cli_red;
        } else {
            echo $cli_red . $i . '. Failed to send!' . PHP_EOL . $cli_red;
        }
    
    }

    sleep(NOTIFY_INTERVAL);
}

