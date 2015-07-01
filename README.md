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

### GET `/v1/history/stats`

Returns a few stats about the usage. For example:

```
{
   "totalLength": 12412,  # Number of seconds the restroom has been used
   "totalCount": 100,  # Number of times restroom has been used
   "averageTime": 124.12,  # Average time, in seconds, that the restroom is used
   "popularity": {  # Number of time per day scaled to a range of 0-10
      "sun": 0,
      "mon": 4,
      "tues": 10,
      "wed": 9,
      "thur": 2.4,
      "fri": 5.76,
      "sat": 0.33
   },
   "rawPopularity": [  # Raw number of time per day distribution
      0,
      3,
      40,
      30,
      10,
      30,
      7
   ]
}
```

### GET `/v1/history/day`

Returns the per hour of the day stats. This is date agnositc, it only cares about the hour of the day. Each hour field contains:

```
{
   "count": 2,  # Number of times used in this hour of any day
   "percent": 0.5,  # Percentage of this hour when is use
   "timeUsed": 100,  # Amount of time in seconds used
   "secondsTracked": 200  # Total number of seconds tracked
}
```


## Sections for project
* [Web Client](https://github.com/onebytegone/restroom-monitor-web)
* [API](https://github.com/onebytegone/restroom-monitor-server)
* [Serial/API bridge](https://github.com/onebytegone/restroom-monitor-updater)
* [Arduino Sensor](https://github.com/onebytegone/restroom-monitor-arduino)
