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


## Endpoints

### GET `/v1/status`

Returns the status, voltage, and last updated time


## Sections for project
* [Web Client](https://github.com/onebytegone/restroom-monitor-web)
* [API](https://github.com/onebytegone/restroom-monitor-server)
* [Serial/API bridge](https://github.com/onebytegone/restroom-monitor-updater)
