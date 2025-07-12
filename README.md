<p align="left">
  <img src="https://api.sadiq.workers.dev/app/github/repo/exam-reminder/views" alt="Repo views" />
</p>

# Exam Reminder

A simple PHP-based notification tool to help you remember to study for your exams! This script sends reminders via email and Telegram to a list of recipients at regular intervals, encouraging them to keep studying until the exam time.

## Features

- Sends reminders to recipients via **Email** and **Telegram Bot**
- Customizable notification interval and deadline
- Support for multiple recipients loaded from a file
- Bengali/English message support
- CLI-friendly output with colored messages

## How It Works

- The script (`notifier.php`) runs in a loop until a specified ending time (`NOTIFY_UNTILL`).
- On each interval, it sends a reminder message to each recipient, via both email and Telegram (if their details are present).
- Messages include the time left until the exam and motivational text.

## Prerequisites

- PHP 7.4 or higher
- [Composer](https://getcomposer.org/) (for dependencies)
- A Telegram Bot Token (for Telegram notifications)
- SMTP credentials (for sending emails)
- A `recipients` file (CSV or similar, see below)

## Installation

1. **Clone the repository**
    ```bash
    git clone https://github.com/sadiq-bd/exam-reminder.git
    cd exam-reminder
    ```

2. **Install dependencies**
    ```bash
    composer install
    ```

3. **Configure**
    - Copy and edit `config.inc.php.example` to `config.inc.php` (if provided), or create your own.
    - Set up the following in `config.inc.php`:
      - `NOTIFY_UNTILL` (timestamp)
      - `NOTIFY_INTERVAL` (seconds)
      - `RECIPIENTS_FILE` (path to recipients list)
      - `TELEGRAM_BOT_API` (token)
      - SMTP credentials for `sendMail`

4. **Prepare Recipients File**
    - The recipients file should be a CSV or similar format with at least these columns:
        - `name`
        - `email`
        - `tg_chat_id` (for telegram bot; optional)

5. **Run the Notifier**
    ```bash
    php notifier.php
    ```

## Example Recipient File

```csv
name,email,tg_chat_id
Alice,alice@example.com,123456789
Bob,bob@example.com,
```

## Customization

- Edit the messages and HTML in `notifier.php` to personalize the reminder.
- Add or remove fields as needed for your use-case.

## License

MIT License

## Author

- [Sadiq](https://sadiq.us.to)

---

> "আর মাত্র ... ঘণ্টা ... মিনিট বাকি!!! পড় ভাই পড় 💥!!!"  
> _Keep studying, you can do it!_
