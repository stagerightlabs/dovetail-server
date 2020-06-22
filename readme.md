# Dovetail Server

Dovetail is an open source platform for laboratory notebooks, similar to E-LabFTW.  This repository contains the backend server; a Laravel application.

## Local Development

You will need to have PHP 7.1.3+ available on your system, as well as the [Composer](https://getcomposer.org/) dependency manager.

Clone the repo and the move into the project's root directory.

Install the dependencies:

```
composer install
```

Note: the dependencies are currently a bit out of date; I can't guarantee this will work without making some updates to the dependency list.

Set up your local config files:

```
cp .env.example .env
```

Ensure you update your new `.env` file with the appropriate settings for your local system; including the URL for the local instance of the frontend client.

Run the migrations

```
php artisan migrate
```

Set up the [Passport](https://laravel.com/docs/7.x/passport#installation) keys

```
php artisan passport:install
```

You should now be able to launch the backend service locally:

```
php artisan serve
```
