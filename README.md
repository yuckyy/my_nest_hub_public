# My Nest Hub

## Installation

1. install php 7.1
1. install composer
1. install mysql 5.6 or above (use PLAIN password for user)
1. clone project to your local machine using `git clone`
1. enter to project folder
1. install php dependencies run `composer install`
1. generate project key `php artisan key:generate`
1. copy .env.example to .env
1. update configuration of mysql in .env file according your local mysql configuration
1. run migrations `php artisan migrate`
1. upload seeds `php artisan db:seed`
1. run application using `php artisan serve`
1. open application in browser using link from cli
1. Have a good work!
