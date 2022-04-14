<?php

// Includes the config file
include("config.php");

// Get the ID number from the URL
$Battleid = substr($BattlemetricsURL, strrpos($BattlemetricsURL, '/') + 1);

// Make the API URL:
$BattleAPIURL = 'https://api.battlemetrics.com/servers/' . $Battleid;


//Get the JSON output and decode it
$BattleJSON = file_get_contents($BattleAPIURL);
$BattleJSONdata = json_decode($BattleJSON, true);


// Get all the data to variables
$server_name = $BattleJSONdata["data"]["attributes"]["name"];
$server_players = $BattleJSONdata["data"]["attributes"]["players"];
$server_status = $BattleJSONdata["data"]["attributes"]["status"];
$server_lastwipe = $BattleJSONdata["data"]["attributes"]["details"]["rust_last_wipe"];
$server_ip = $BattleJSONdata["data"]["attributes"]["ip"];
$server_port = $BattleJSONdata["data"]["attributes"]["port"];
$server_uptime = $BattleJSONdata["data"]["attributes"]["details"]["rust_uptime"];


// Combines $server_ip with $server_port
$server_ip = $server_ip . ':' . $server_port;


// Makes the Steam Connect URL
$server_steamconnecturl = 'steam://connect/' . $server_ip;


// Function to convert JSON seconds to days-hours-minutes
function secondsToTime($var) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$var");
    return $dtF->diff($dtT)->format('%ad %hh %imin');
}
$server_lastrestarted = secondsToTime($server_uptime);


// Function to convert seconds since wipe to days ago
function dayssincewipe($var) {
    $secondssincewipe = date(strtotime($var)); // Convert the JSON time to Unix time

    $currentTime = date('Y-m-d'); // Get the current time
    $timestamp = strtotime($currentTime); // Convert it to seconds since Jan 01 1970 (Unix time stamp)

    $timediff = $timestamp - $secondssincewipe; // Seconds since Jan 01 1970 - Seconds since wipe

    return round($timediff / (60 * 60 * 24)); // Multiplies with seconds to minutes to hours to days
}
$server_dayssincewipe = dayssincewipe($server_lastwipe);


// Maked colored serverstatuses and changes for example online to Online
if ($ColoredServer_status === true)
{
    if ($server_status == 'online') {
        $server_status = "<span style='color:#1ccf31';>Online</span>";
    } elseif ($server_status == 'offline') {
        $server_status = "<span style='color:#bb2536';>Offline</span>";
    } else {
        $server_status = "<span style='color:#bb2536';>$server_status</span>";
    }
} else {
    if ($server_status == 'online') {
        $server_status = "Online";
    } elseif ($server_status == 'offline') {
        $server_status = "Offline";
    }
}

$OutputHTML = "<p><a href='$server_steamconnecturl'><strong>$server_name</strong></a><br /><strong>State:</strong> $server_status | <strong>Online:</strong> {$server_players} | <strong>Wiped:</strong> {$server_dayssincewipe} days ago</p>";

echo $OutputHTML;

?>