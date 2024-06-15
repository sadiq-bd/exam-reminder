<?php

date_default_timezone_set('Asia/Dhaka');

define('NOTIFY_UNTILL', strtotime('2025-05-30 00:00:00'));

define('NOTIFY_INTERVAL', 60 * 60);      // in seconds

define('RECIPIENTS_FILE', __DIR__ . '/recipients.json');

define('MAIL_HOST', 'smtp.gmail.com');

define('MAIL_USERNAME', 'username@gmail.com');

define('MAIL_PASSWORD', 'password_here');

define('MAIL_FROM', MAIL_USERNAME);
