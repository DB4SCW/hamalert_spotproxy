# Hamalert Spotreceiver
## About
This piece of php receives the json input of a hamalert spot and dumps all data into a sqlite database for reporting and analysis later on.

It can also proxy the json input for a configurable amount of callsigns to other URL receivers, basically creating a hamalert_proxy, for example for [Coordinatorrs Hamalert Integration](https://hamawardz.de/docs/coordinatorr/hamalert_integration/). For this, please copy ```config.json.example``` to ```config.json``` and customize to your liking.

If you only want the proxy feature and don't want the database, that's also possible by adding a file called ```no_database_please.txt``` in the project directory (content optional).
