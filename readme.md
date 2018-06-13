# Prosk Back-end
prosk backend is designed by laravel framework. all the API's are implemented in this project. prosk database is mysql. there is no login and registration in this project and every thing is based on user token. and tokens are generated randomly for browsers. so we can't offer any database dump here.
#### requirement
* php >= `7.1.14`
* composer >= `1.6.3`

#### install project
clone project from github repository by command:
```
git clone https://github.com/mr-exception/prosk-backend.git
```
enter the project directory:
```
cd prosk-backend
```
install all the packages
```
composer install
```
create an empty database. then rename `.env.example` file to `.env` in project root directory and fill all the needed informations as database name, password and ...
then generate a new key by command:
```
php artisan key:generate
```
then run the migrations to create all the tables in databse:
```
php artisan migrate
```
run the project:
```
php artisan serve
```