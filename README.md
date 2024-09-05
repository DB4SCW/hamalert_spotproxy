# Hamalert Spotreceiver
## About
This piece of php receives the json input of a hamalert spot and dumps all data into an sqlite database for reporting later on.

It can also proxy the json input for a configurable amount of callsigns to other URL receivers, basically creating a hamalert_proxy, for example for [Coordinatorrs Hamalert Integration](https://hamawardz.de/docs/coordinatorr/hamalert_integration/).