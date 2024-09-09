# Hamalert Spotproxy
## About
This piece of php receives the json input of a hamalert spot and proxies this data do a configurable number of other URL endpoints, depending on callsign of the spot or trigger of the spot.

Additionally, it can dump all data into a sqlite database for reporting and analysis later on.

Example use could be [Coordinatorrs Hamalert Integration](https://hamawardz.de/docs/coordinatorr/hamalert_integration/). 

## Installation

### Step 1: Clone repo
Clone this repo!

```bash
cd /var/www
sudo -u www-data git clone https://github.com/DB4SCW/hamalert_spotproxy hamalert_spotproxy
```

### Step 2: Create config
Enter the new directory and clone the example config file

```bash
cd hamalert_spotproxy
sudo -u www-data cp config.json.example config.json
```

### Step 3: Modify your config
If you like to only proxy spots for specific callsigns, fill out the "callsigns" section of the config:

You can provide 1 URL per callsign like that as a string. For an example, see the "N0CALL" line in the example config.

If you like to have hamalert spotproxy sent this spot to multiple endpoints, provide an array of URLs, like in the "N1CECALL" line in the example config.

If you like to proxy based on trigger name, fill out the "trigger" section of the config. Just as with callsigns, you can provide a single endpoint or an array of endpoints.

If you like hamalert spotproxy to store all the spots it receives in a sqlite database, provide ```true``` to the key ```use_database```. If you don't like to store your spots, provide ```false``` or omit this key altogether.

You can also provide a database name you like by modifying the key ```filename```. If you leave this empty or omit this key, but turn on the databse feature, the file will be named ```spots.sqlite```.

### Step 4: Webserver
Point your webserver to the directory of this project (if you followed this instructions, ```/var/www/hamalert_spotproxy```) and make sure to:
- enable your webserver to read .htaccess files and process them
- enable the rewrite engine on your webserver