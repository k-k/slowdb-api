SlowDB HTTP API
===============

A simple HTTP API written using the Symfony2 Framework.

### Install

The best way to install the API is through the use of [Composer](http://getcomposer.org/download).

Clone down the repository and run composer install:

    $> git clone git@github.com:kmfk/slowdb-api.git
    $> cd slowdb-api
    $> php composer.phar install --no-dev

The SlowDB API includes the [SlowDB](https://github.com/kmfk/slowdb) library.

### Usage

##### The Database Server

First start the SlowDB server:

    $> ./vendor/bin/slowdb

By default this will bind the server to port `1337` on the localhost address.

##### The HTTP Server

The easiest way to access the HTTP API is through the built in PHP Server. Run
the provided Symfony console command to start the server:

    $> php app/console server:run 127.0.0.1:8080

This will listen for web requests on port `8080`.

Optionally, you can configure a vhost (apache or nginx) to point to this API.

### Supported Endpoints

    $> php app/console router:debug

       Name                Method Scheme Host Path
       collection_all      GET    ANY    ANY  /tables/{collection}
       collection_truncate DELETE ANY    ANY  /tables/{collection}
       collection_get      GET    ANY    ANY  /tables/{collection}/{key}
       collection_search   GET    ANY    ANY  /tables/{collection}/search?q={query}
       collection_count    GET    ANY    ANY  /tables/{collection}/count?q={query}
       collection_set      POST   ANY    ANY  /tables/{collection}
       collection_put      PUT    ANY    ANY  /tables/{collection}/{key}
       collection_delete   DELETE ANY    ANY  /tables/{collection}/{key}
       database_all        GET    ANY    ANY  /tables
       database_drop       DELETE ANY    ANY  /tables
       database_search     GET    ANY    ANY  /tables/search?q={query}
