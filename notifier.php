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

    $interval = NOTIFY_INTERVAL / 60;   // in minutes

    foreach (getRecipients(RECIPIENTS_FILE) as $j => $recipient) {
        $i++;
        $name = $recipient['name'];
        $email = @$recipient['email'];
        $tg_chat_id = @$recipient['tg_chat_id'];

        $hours = intval($time_left_in_hours) . ' ঘণ্টা ';
        $mins = ($time_left_in_mins % 60) . ' মিনিট ';

        $subject = en2bn('আর মাত্র ' . $hours . $mins . 'বাকি!!! পড় ভাই পড় 💥!!!');

        if ($tg_chat_id) {
            $http = new \GuzzleHttp\Client();
            $response = $http->post(
                'https://api.telegram.org/bot'.TELEGRAM_BOT_API.'/sendMessage',
                [
                    'json' => [
                        'chat_id' => $tg_chat_id,
                        'text' => $subject
                    ]
                ]
            );
            $resp = @json_decode($response->getBody(), true);
            if (!empty($resp['ok']) && $resp['ok'] == true) {
                echo $cli_green . $i . '. Telegram Message Sent Successfully to - ' . $name . PHP_EOL . $cli_red;
            } else {
                echo $cli_red . $i . '. Failed to send telegram message!' . PHP_EOL . $cli_red;
            }
        }

        if ($email) {
            $body = <<<MSGBODY
        
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>{$subject}</title>
                </head>
                <body>
                    <h5>প্রিয় {$name}, </h5><br>

                    <p>
                        {$subject}
                    </p>
                    <h2 style="color:red;">অনুগ্রহ করে পড়তে বসুন</h2>

                    <br>
                    <hr>
                    <br>

                    <center>আপনার শুভাকাঙ্ক্ষী - <a href="https://sadiq.us.to">সাদিক</a></center>

                    <br>
                    <br>
                    <br>
                    <small>Next Remind after {$interval} minutes</small>

                </body>
                </html>
        
            MSGBODY;

            $from = 'Time_Left_' . intval($time_left_in_hours) . '_Hours';

            if (sendMail($email, $subject, $body, $from)) {
                $sentMail = is_array($email) ? implode(',', $email) : $email; 
                echo $cli_green . $i . '. Mail Sent Successfully to - ' . $name . '<'. $sentMail .'>'  . PHP_EOL . $cli_red;
            } else {
                echo $cli_red . $i . '. Failed to send!' . PHP_EOL . $cli_red;
            }
        }
    
    }

    echo $cli_default . "\n\n" . '-- Interval (' . NOTIFY_INTERVAL . 's) --' . "\n\n"; 
    sleep(NOTIFY_INTERVAL);
}

