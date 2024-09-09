<?php

    function main()
    {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] != 'POST') 
        {
            die("Please send a POST request.");
        }
        
        //check if config file exists and decide if hamalertproxy capabilities are needed
        $skip_hamalert_proxy = true;
        $config = [];
        $callsignconfig = [];
        $triggerconfig = [];
        if(file_exists("config.json"))
        {
            //load config.json
            try {
                $config = json_decode(file_get_contents("config.json", true), true);
            } catch (\Throwable $th) {
                die("invalid JSON file");
            }
            
            //extract callsign config if present
            if(array_key_exists("callsigns", $config))
            {
                $callsignconfig = $config['callsigns'];
            }

            //extract trigger config if present
            if(array_key_exists("triggers", $config))
            {
                $triggerconfig = $config['triggers'];
            }

            //set skip flag
            $skip_hamalert_proxy = false;
        }

        // Define the database file
        $dbFile = 'spots.sqlite';

        //check if flagfile exist to skip persitent sqlite storage - use this if you only want the proxy functionality
        if(file_exists('no_database_please.txt'))
        {
            //uses an in-memory-db to prevent creation of a persistant database file
            $dbFile = ":memory:";
        }

        // Create (open) SQLite database connection
        $db = new PDO('sqlite:' . $dbFile);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create the 'spots' table if it doesn't exist
        $db->exec("
            CREATE TABLE IF NOT EXISTS spots (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
                fullCallsign TEXT,
                callsign TEXT,
                frequency TEXT,
                band TEXT,
                mode TEXT,
                modeDetail TEXT,
                time TEXT,
                spotter TEXT,
                rawText TEXT,
                title TEXT,
                comment TEXT,
                source TEXT,
                qsl TEXT,
                dxcc INTEGER,
                entity TEXT,
                cq INTEGER,
                continent TEXT,
                homeDxcc INTEGER,
                homeEntity TEXT,
                spotterDxcc INTEGER,
                spotterEntity TEXT,
                spotterCq INTEGER,
                spotterContinent TEXT,
                triggerComment TEXT,
                speed INTEGER,
                snr INTEGER,
                state TEXT,
                spotterState TEXT,
                iotaGroupRef TEXT,
                iotaGroupName TEXT,
                summitName TEXT,
                summitHeight TEXT,
                summitPoints INTEGER,
                summitRef TEXT,
                wwffName TEXT,
                wwffDivision TEXT,
                wwffRef TEXT,
                proxied TEXT
            )
        ");

        // Get the raw POST data (which is JSON)
        $rawData = file_get_contents('php://input');
            
        // Decode the JSON into an associative array
        $jsonData = json_decode($rawData, true);

        // Check if the data is valid JSON
        if (json_last_error() != JSON_ERROR_NONE) {
            die("Invalid JSON received");
        } 

        // Prepare the SQL query to insert data into the 'spots' table
        $stmt = $db->prepare("
        INSERT INTO spots (
            fullCallsign, callsign, frequency, band, mode, modeDetail, time, spotter, rawText, 
            title, comment, source, qsl, dxcc, entity, cq, continent, homeDxcc, homeEntity, 
            spotterDxcc, spotterEntity, spotterCq, spotterContinent, triggerComment, speed, snr, state, spotterState,
            iotaGroupRef, iotaGroupName, summitName, summitHeight, summitPoints, summitRef, wwffName, wwffDivision, wwffRef
        ) VALUES (
            :fullCallsign, :callsign, :frequency, :band, :mode, :modeDetail, :time, :spotter, :rawText, 
            :title, :comment, :source, :qsl, :dxcc, :entity, :cq, :continent, :homeDxcc, :homeEntity, 
            :spotterDxcc, :spotterEntity, :spotterCq, :spotterContinent, :triggerComment, :speed, :snr, :state, :spotterState,
            :iotaGroupRef, :iotaGroupName, :summitName, :summitHeight, :summitPoints, :summitRef, :wwffName, :wwffDivision, :wwffRef
        )
        ");

        // Bind values from the decoded JSON data
        $stmt->bindValue(':fullCallsign', $jsonData['fullCallsign'] ?? null);
        $stmt->bindValue(':callsign', $jsonData['callsign'] ?? null);
        $stmt->bindValue(':frequency', $jsonData['frequency'] ?? null);
        $stmt->bindValue(':band', $jsonData['band'] ?? null);
        $stmt->bindValue(':mode', $jsonData['mode'] ?? null);
        $stmt->bindValue(':modeDetail', $jsonData['modeDetail'] ?? null);
        $stmt->bindValue(':time', $jsonData['time'] ?? null);
        $stmt->bindValue(':spotter', $jsonData['spotter'] ?? null);
        $stmt->bindValue(':rawText', $jsonData['rawText'] ?? null);
        $stmt->bindValue(':title', $jsonData['title'] ?? null);
        $stmt->bindValue(':comment', $jsonData['comment'] ?? null);
        $stmt->bindValue(':source', $jsonData['source'] ?? null);
        $stmt->bindValue(':qsl', $jsonData['qsl'] ?? null);
        $stmt->bindValue(':dxcc', $jsonData['dxcc'] ?? null);
        $stmt->bindValue(':entity', $jsonData['entity'] ?? null);
        $stmt->bindValue(':cq', $jsonData['cq'] ?? null);
        $stmt->bindValue(':continent', $jsonData['continent'] ?? null);
        $stmt->bindValue(':homeDxcc', $jsonData['homeDxcc'] ?? null);
        $stmt->bindValue(':homeEntity', $jsonData['homeEntity'] ?? null);
        $stmt->bindValue(':spotterDxcc', $jsonData['spotterDxcc'] ?? null);
        $stmt->bindValue(':spotterEntity', $jsonData['spotterEntity'] ?? null);
        $stmt->bindValue(':spotterCq', $jsonData['spotterCq'] ?? null);
        $stmt->bindValue(':spotterContinent', $jsonData['spotterContinent'] ?? null);
        $stmt->bindValue(':triggerComment', $jsonData['triggerComment'] ?? null);
        $stmt->bindValue(':speed', $jsonData['speed'] ?? null);
        $stmt->bindValue(':snr', $jsonData['snr'] ?? null);
        $stmt->bindValue(':state', $jsonData['state'] ?? null);
        $stmt->bindValue(':spotterState', $jsonData['spotterState'] ?? null);
        $stmt->bindValue(':iotaGroupRef', $jsonData['iotaGroupRef'] ?? null);
        $stmt->bindValue(':iotaGroupName', $jsonData['iotaGroupName'] ?? null);
        $stmt->bindValue(':summitName', $jsonData['summitName'] ?? null);
        $stmt->bindValue(':summitHeight', $jsonData['summitHeight'] ?? null);
        $stmt->bindValue(':summitPoints', $jsonData['summitPoints'] ?? null);
        $stmt->bindValue(':summitRef', $jsonData['summitRef'] ?? null);
        $stmt->bindValue(':wwffName', $jsonData['wwffName'] ?? null);
        $stmt->bindValue(':wwffDivision', $jsonData['wwffDivision'] ?? null);
        $stmt->bindValue(':wwffRef', $jsonData['wwffRef'] ?? null);

        //Execute the SQL query to insert the data
        $stmt->execute();

        //Respond to the client
        echo "Data saved to " . $dbFile . "\r\n";

        //Get the ID of the inserted row
        $insertedId = $db->lastInsertId();

        //check if hamalertproxy is to be performed. If not, abort early.
        if($skip_hamalert_proxy)
        {
            return;
        }

        //get callsign data from input json
        $callsign = $jsonData['callsign'] ?? null;
        $trigger = $jsonData['triggerComment'] ?? null;
        
        //run proxy for every call if ":ALLCALL:" exists in the config
        if(array_key_exists(":ALLCALL:", $callsignconfig))
        {
            //get destination(s)
            $destinations = $callsignconfig[':ALLCALL:'];
            
            //run proxy
            runproxy($callsign, $destinations, $rawData, $db, $insertedId, "allcall");
        }

        //run proxy if callsign explicitly exists inside the callsignconfig
        if (array_key_exists($callsign, $callsignconfig)) {
            
            //get destination(s)
            $destinations = $callsignconfig[$callsign];

            //run proxy
            runproxy($callsign, $destinations, $rawData, $db, $insertedId, "callsign");
        }

        //run proxy for triggers
        if(array_key_exists($trigger, $triggerconfig))
        {
            //get destination(s)
            $destinations = $triggerconfig[$trigger];

            //run proxy
            runproxy($callsign, $destinations, $rawData, $db, $insertedId, "trigger");            
        }

        //end function
        return;
    }

    function runproxy($callsign, $destinationraw, $rawData, $db, $insertedId, string $type)
    {
        //determine todo variable
        $destinations = [];

        //determine if it is a singular destination or not and add it to the todo list
        if(is_array($destinationraw))
        {   
            foreach ($destinationraw as $dest) {
                array_push($destinations, $dest);
            }
        }elseif(is_string($destinationraw))
        {
            array_push($destinations, $destinationraw);
        }else
        {
            die("Invalid destination for proxy in config.json\r\n");
        }

        //proxy for each todo destination
        foreach ($destinations as $destination) {
            hamalert_proxy($rawData, $destination, $db, $insertedId, $destinations);
            echo ucfirst($type) . " Hamalert proxy for callsign " . $callsign . " to " . $destination . " performed successfully.\r\n";
        }

    }

    function hamalert_proxy(string $raw_data, $destination, $db, int $dbid, $multiple = null)
    {
        //Use curl to send a POST request of the original raw data
        $ch = curl_init($destination);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($raw_data))
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw_data);

        //Execute the request and get the response
        $response = curl_exec($ch);

        //Check for curl errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            die('Error sending POST request to proxy: ' . $error);
        }

        //close curl
        curl_close($ch);

        //set proxy URL to spot
        $stmt = $db->prepare("UPDATE spots SET proxied = :proxied WHERE id = :id;");
        $stmt->bindValue(':proxied', $multiple == null ? $destination : json_encode($multiple));
        $stmt->bindValue(':id', $dbid);

        //Execute the SQL query to update data
        $stmt->execute();
    }


    //run main function
    main();
?>
