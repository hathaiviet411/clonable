## Installation 
### Server Requirements

- PHP version 7.4.11
- MySQL version 8.0.21
- Composer
- Git
- NPM

### === Change config PHP .int file  ====
```terminal

max_input_time=6000
max_execution_time=1200
upload_max_filesize=1G
memory_limit=1G

```
### 1. Command install 

```terminal
chmod -R 777 storage/
chmod -R 776 storage/framework

composer update

#install npm
npm install
```

### 2. Make environment configuration  
```terminal
cp .env.example .env

cp .env.testing.example .env.testing

```

### 3. Configuration database connection in .env file
```terminal
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=my_db_name
DB_USERNAME=my_db_user
DB_PASSWORD=my_password
```

### 4. Migrate database and seeder

```terminal
php artisan key:generate

php artisan jwt:secret

php artisan l5-swagger:generate 

php artisan reload:cache

php artisan migrate

php artisan db:seed
```


### 5. Run project in localhost

```terminal
npm run prod
php artisan queue:w --timeout=0
```

### 6. Run test
```terminal
Before Unit test, setting enviroment test
--create file 'database.sqlite' in folder database

-For FE test
npm run test

After running the test, please refresh the DB to avoid heavy test data file and recreate the account. Because after each test run the user table will lose data.

command to fresh test DB: php artisan migrate:fresh --seed --env=testing 

-run all file Unit Test
php artisan migrate:fresh --seed --env=testing 
php artisan test --testsuite=Unit --env=testing 

-run all file Feature
php artisan migrate:fresh --seed --env=testing 
php artisan test --testsuite=Feature --env=testing

-run all file 
php artisan migrate:fresh --seed --env=testing 
php artisan test --env=testing

#install dusk test
php artisan dusk:install

# run test dusk
run test scenario 
-run all file
php artisan dusk tests/Browser/scenario
-run one file
php artisan dusk tests/Browser/scenario/ + file name
Example:php artisan dusk tests/Browser/scenario/Scenario1Test.php


### 7. Additional settings for the environment on the server

```terminal
#install pm2 
npm i -g pm2

#start service
pm2 start queue-worker.yml

# config auto start if reboot server
pm2 startup
pm2 save

```

```Show multi language
http://localhost:8000?lang=1
```
