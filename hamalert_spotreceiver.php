<?php
// Define the database file
$dbFile = 'spots.sqlite';

// Create (open) SQLite database connection
$db = new PDO('sqlite:' . $dbFile);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') 
{
    die("Please send a POST request.");
}

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
        wwffRef TEXT
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

// Execute the SQL query to insert the data
$stmt->execute();

// Respond to the client
echo "Data saved to spots.sqlite";

return;
?>
