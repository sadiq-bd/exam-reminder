<?php

use Spatie\Async\Pool;

require __DIR__ . '/config.inc.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.inc.php';

$cli_green = "\033[1;92m";
$cli_red = "\033[1;91m";
$cli_default = "\033[1;39m";

$videos = glob(__DIR__ . '/videos/*.mp4');
$videoFile = function () use ($videos) {
  return $videos[array_rand($videos)];
};
$videoFileStream = function ($videoFile) {
  return fopen($videoFile, 'r');
};
$i = 0;

echo $cli_default . "\n\n" . '-- Exam Reminder --' . "\n\n";

while (time() < NOTIFY_UNTILL) {
  if (date('i') % (NOTIFY_INTERVAL / 60) == 0) {
    $time_left_in_secs = NOTIFY_UNTILL - time();
    $time_left_in_mins = $time_left_in_secs / 60;
    $time_left_in_hours = $time_left_in_mins / 60;
    $time_left_in_days = $time_left_in_hours / 24;

    $interval = NOTIFY_INTERVAL / 60;   // in minutes

    $pool = Pool::create();

    foreach (getRecipients(RECIPIENTS_FILE) as $j => $recipient) {
      $i++;
      $name = $recipient['name'];
      $email = isset($recipient['email']) ? $recipient['email'] : null;
      $tg_chat_id = isset($recipient['tg_chat_id']) ? $recipient['tg_chat_id'] : null;

      $hours = intval($time_left_in_hours) . ' ঘণ্টা ';
      $mins = ($time_left_in_mins % 60) . ' মিনিট ';

      $subject = en2bn('আর মাত্র ' . $hours . $mins . 'বাকি!!! পড় ভাই পড় 💥!!!');

      $video = $videoFile();

      $pool->add(function () use ($i, $time_left_in_hours, $interval, $name, $email, $tg_chat_id, $hours, $mins, $subject, $video, $videoFileStream) {

        global $cli_green;
        global $cli_red;
        global $cli_default;

        if ($tg_chat_id) {
          $http = new \GuzzleHttp\Client();
          $sendVid = $http->post(
            'https://api.telegram.org/bot' . TELEGRAM_BOT_API . '/sendVideo',
            [
              'multipart' => [
                [
                  'name'     => 'video',
                  'contents' => $videoFileStream($video),
                  'filename' => basename($video),
                ],
                [
                  'name' => 'chat_id',
                  'contents' => $tg_chat_id,
                ],
                [
                  'name' => 'caption',
                  'contents' => $subject,
                ]
              ]
            ]
          );
          $resp = [
            @json_decode($sendVid->getBody(), true)
          ];
          if (
            !empty($resp[0]['ok'])
            && $resp[0]['ok']
          ) {
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
            echo $cli_green . $i . '. Mail Sent Successfully to - ' . $name . '<' . $sentMail . '>'  . PHP_EOL . $cli_red;
          } else {
            echo $cli_red . $i . '. Failed to send!' . PHP_EOL . $cli_red;
          }
        }
      })->then(function ($output) {
        // Handle success
      })->catch(function (Throwable $exception) {
        // Handle exception
      });
    }

    echo $cli_default . "\n\n" . '-- Interval (' . NOTIFY_INTERVAL . 's) --' . "\n\n";
  }
  sleep(60);
}
