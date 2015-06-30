# Restroom Monitor: Server

This is the server for the restroom monitor project.

This handles storing the status of the restroom.


## Setup

### Configuration
The base config can be duplicated by running:
```
cp environment.example.php environment.php
```

It should be noted that `PRIVATE_SHARED_JWT_KEY` should probally be changed.


## Unit Tests

The unit tests require PHPUnit. They can be ran by calling:
```
util/run-tests.sh
```


## Endpoints

### GET `/v1/status`

Returns the status, voltage, and last updated time

### GET `/v1/history/raw/:type/(:limit)`

**Segments:**

  * `type` The type of data to return. Possible values: `status`, `comm`, and `voltage`
  * `limit` The max number of items to return

This provides access to the raw log information


## Sections for project
* [Web Client](https://github.com/onebytegone/restroom-monitor-web)
* [API](https://github.com/onebytegone/restroom-monitor-server)
* [Serial/API bridge](https://github.com/onebytegone/restroom-monitor-updater)
* [Arduino Sensor](https://github.com/onebytegone/restroom-monitor-arduino)
