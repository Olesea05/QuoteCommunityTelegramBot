# TelegramBot

A friendly Telegram bot for quote lovers. Share your favorite quotes with the community and receive a daily quote from other members! 📚✨

## Features

- Receive a random approved quote every day.
- Submit your own quote for the community.
- Administrative panel for moderating and editing user-submitted quotes.
- Simple command panel for easy navigation:
  - `/start` – start the bot
  - `/help` – see all commands
  - `/new` – submit a new quote


## Technologies

- **PHP** 
- **Laravel** 
- **Telegram Bot API** 
- **MySQL** 
- **Composer**


## Installation

1. Clone the repository:
   ```bash
   git clone <your-repo-url>

2. Navigate to the project folder:
   ```bash
   cd quote-community-bot

3. Install dependencies:
   ```bash
   composer install

4. Copy the example environment file and set your configuration:
   ```bash
   cp .env.example .env

5. Run database migrations:
   ```bash
   php artisan migrate

6. Run the Laravel server:
   ```bash
   php artisan serve

7. Set up your Telegram webhook to point to:
   ```bash
   https://your-domain.com/api/webhook 


Author: Olesea05