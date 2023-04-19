
## Dublemint-Test
It took me 5 hours to complete the test task

- PHP 8.2.4
- Node v18.15.0
- npm version 9.6.2
- Laravel version 10.x
- Vue 3
- MySQL

## LARAVEL 10.x
- Download or clone this repository
- ```cd Dublemint-Test```
- ```composer install``` or ```php composer.phar install``` depends on whether Composer is installed globally or locally.
- Replace ```APP_URL``` with ```APP_URL=http://localhost:8000``` ❗IT IS IMPORTANT❗
- Don`t forget to replace ```DB_DATABASE, DB_USERNAME and DB_PASSWORD``` in .env file
- ```php artisan key:generate```
- ```php artisan migrate:fresh --seed```
- ```php artisan serve```

- Go to http://localhost:8000

## VUE 3
Project`s  front is ❗ALREADY COMPILED❗ with ```vite```.

BUT in a case you have encountered a problem, perform the following steps to rebuild the frontend:

- ```npm install```
- ```npm run build``` or ```vite build```


For any questions, please contact [@vsarapin](https://t.me/vsarapin)
