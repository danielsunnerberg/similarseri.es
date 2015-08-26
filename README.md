[similarseri.es](http://similarseri.es) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/8cd9d924-910b-4292-b8fd-cdf977371cd2/big.png)](https://insight.sensiolabs.com/projects/8cd9d924-910b-4292-b8fd-cdf977371cd2) [![Build Status](https://travis-ci.org/danielsunnerberg/similarseri.es.svg?branch=master)](https://travis-ci.org/danielsunnerberg/similarseri.es)
==============

Predicts what the user would like to watch next by analyzing previously watched shows.

## Contributing

A fully-functional local version can be setup quite easily after cloning this repository.

### Additional requirements
- Composer
- Bower
- RabbitMQ
- A TMDB-API-key
- Ruby

### Typical installation
```
composer install
bower install
./app/console assetic:dump
./app/console doctrine:database:create
./app/console doctrine:schema:update --force
```

*(Excluding operations such as setting cache permissions and other [Symfony generic tasks](http://symfony.com/doc/current/book/installation.html))*

### Deploying
Deploying the application is done using Capistrano. Edit the parameters in `app/config/deploy.rb` and then run:

```
cap deploy:cold # or cap:deploy if it isn't the first deploy
cap symfony:doctrine:database:create
cap symfony:doctrine:mongodb:schema:update
```

### Understanding queues
Due to limitations in the TMDB-API, fetching fully fetching a show's similar shows require one request per similar show. This is extremely time consuming, which is why they instead are queued up to later on be processed by RabbitMQ consumers.

To consume shows from the queues, run:
```
./app/console rabbitmq:consumer -m 5 show_fetcher
./app/console rabbitmq:consumer -m 5 show_patcher
```
In a production enviroment, these should be run from e.g. crontab.

### Making it run fast
As previosuly mentioned, fetching shows from the TMDB-API is slow. To make it run fast for users who adds uncached(*) shows, we can guess what shows will be like added. This is done by the `similarseries:show:fetch <source> <count>` command. E.g.:
```
./app/console similarseries:show:fetch popular 500
```
**An uncached show is a show which hasn't previosuly been cached after being downloaded by another user within the last 90 days.*

