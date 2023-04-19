
## Dublemint-Test
It took me 5 hours to complete the test task

- PHP 8.2.4
- Node v18.15.0
- npm version 9.6.2


Vue.js already compiled with VITE.

## Steps

- Download or clone this repository
- ```cd Dublemint-Test```
- ```composer install``` or ```php composer.phar install``` depends on whether Composer is installed globally or locally.
- Replace ```APP_URL``` with ```APP_URL=http://localhost:8000``` <span style="color:red">IT IS IMPORTANT</span>
- Don`t forget to replace ```DB_DATABASE, DB_USERNAME and DB_PASSWORD``` in .env file
- ```php artisan key:generate```
- ```php artisan migrate --seed```
- ```php artisan serve```
- Go to http://localhost:8000

For any questions, please contact [@vsarapin](https://t.me/vsarapin)
