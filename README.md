# IP Geolocation & Exchange Rate Service

The service is free. Maxmind-DB is used for geographic location determination. The European Central Bank and the Central Bank of the Republic of Turkey used to exchange rates.

## Development

Written using Swoole Http Server. It achieves an average "35k/sec" request on a 4-core machine. 

#### PHP Library

* [Swoole](https://github.com/swoole/swoole-src)
* [FastRoute](https://github.com/nikic/FastRoute)
* [Maxmind Reader](https://github.com/maxmind/MaxMind-DB-Reader-php)

#### Automatic Update

Swoole Scheduler is made with continuous update. The update is checked every 24 hours. The database is downloaded 2 times in one month.

## Installation

You can run the api with Docker.

```yaml

version: '3.4'

services:
    ip-geolocator:
        image: isbkch/ip-geolocator-swoole
        ports:
            - 90:9500
        environment:
            - SWOOLE_PORT=9500
            - SWOOLE_WORKER=2
```

#### Geo Location API

Geo locate client:

```http request
GET http://127.0.0.1:90/geolocate
```

Custom IP address:

```http request
GET http://127.0.0.1:90/geolocate/IP
```

