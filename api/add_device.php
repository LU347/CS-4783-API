<?php
if ($device_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "add_device", "");
    echo $responseData;
	die();
}

$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?device_id=" . $device_id;
$result = call_api($url);
$resultsArray = json_decode($result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

if (strcmp($status, "ERROR") == 0)
{
	$responseData = create_header("ERROR", $msg, "query_device", "");
    echo $responseData;
	die();
} 

$dblink = db_connect("equipment");

if (strcmp($status, "Success") == 0)
{
	$sql = "INSERT INTO devices (device_type, status) 
	VALUES ('$device_id', 'ACTIVE')";
	
	try {
		$result = $dblink->query($sql);
	} catch(Exception $e) {
		$errorMsg = "Error with SQL" . $e;
		$responseData = create_header("ERROR", $errorMsg, "add_device", "");
    	echo $responseData;
		die();
	}
	$responseData = create_header("Success", "Device successfully added!", "add_device", "");
	echo $responseData;
	die();
}
$dblink->close();
die();
?>