# TrackYourStats

TrackYourStats is an affiliate tracking software written in Laravel.

## Getting Started
### Prerequisites
- LAMP with at least PHP 7.0.0
### Installing
- Clone the repository.
```
git clone git@github.com:Leadmax/trackyourstats.git
```
- Copy the example environment file, and set your custom variables.
```
cp .env.example .env
```
Setup your .env file and make sure to set the TYS specific variables.
```
MASTER_DB variables are for your master database connection.
DB_DATABASE is dynamically set.
TYS_BASE_INSTALL - legacy database SQL dump file
TYS_LANDERS_DIRECTORY - directory to store landing designs for your companies.
GEO_IP_DATABASE - location for the GeoIP database.
SALES_LOG_DIRECTORY - where you want to store sale log images.
```
- Install package dependencies.
```
composer install 
```
- Install the legacy database to your master database.
```
php artisan migrate:legacy nameofyourdatabase
```
- Run migrations.
```
php artisan migrate
```

- Setup Laravel's Scheduler
    
    https://laravel.com/docs/5.7/scheduling#introduction
    

## Adding companies
https://github.com/Leadmax/trackyourstats/blob/master/ADDING_COMPANIES.md


## Features
- TrackYourStats Artisan Commands
```
MigrateAllInstalls - migrate:all - Runs migrations against all company databases and master.
MigrateSingleCompany - migrate:single {databasename} - Runs migrations against a single database.
MigrateLegacyDatabase- migrate:legacy {database} - Imports the legacty SQL file into a database.
AggregateReportData - report:aggregate {--start=} {--end=} {--truncate} - Aggregates report data for easier querying. Ran daily aggregating the previous day.
PayoutLogsRun - payout-logs:run {--start=} {--end=} {--truncate} - Aggregates report data specific for payout reports. Ran weekly.
Note: Commands that aggregate data are ran for all companies, including the master database.
```
## Built with
- [ Laravel ] https://laravel.com
- [barryvdh/laravel-snappy] https://github.com/barryvdh/laravel-snappy.git
- [geoip2/geoip2] 
## Versioning
lol

## Authors
   - **Dean Martin** - *Core Developer*, *Maintainer* - [vulski] (https://github.com/vulski)
   - **Joe Randazzo** - *Original Core Developer* - [joerandazzo76] (https://github.com/joerandazzo76)
   - **Matteo Cirami** - *UI Designer* - [mcirami] (https://github.com/mcirami)
   
   
See also the list of [contributors](https://github.com/Leadmax/trackyourstats/contributors) who participated in this project.

