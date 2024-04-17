<?php
/*
$device_id = $_REQUEST['device_id'];
$updated_str = $_REQUEST['updated_str'];
*/
$dblink = db_connect("equipment");

if ($device_id == NULL)
{
	$responseData = create_header("ERROR", "Device ID missing or invalid", "update_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($updated_str == NULL)
{
	$responseData = create_header("ERROR", "Missing new device type", "update_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} else if (ctype_digit($updated_str) == true)
{
	$responseData = create_header("ERROR", "New device is not a string", "update_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//need to query_device to see if the device id is valid, then get update the device id with the updated str
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?method=get_device_type&device_id=" . $device_id;
$result = call_api($url);
$resultsArray = json_decode($result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));

if (strcmp($status, "ERROR") == 0)
{
	$responseData = create_header("ERROR", "Invalid Device Id", "query_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if (strcmp($status, "Success") == 0) //There is a device associated with the given id
{
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?method=check_duplicates&device_type=" . $updated_str;
	$result = call_api($url);
	$resultsArray = json_decode($result, true);
	$status = trim(get_msg_status($resultsArray));
	
	if (strcmp($status, "ERROR") == 0) //means the updated device already exists
    {
		$responseData = create_header("ERROR", "The updated device is already in the database", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
    }
	
	if (strcmp($status, "Success") == 0)
    {
		$sql = "UPDATE devices SET device_type ='" . strtolower($updated_str) . "' WHERE auto_id=" . $device_id;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql: $e", "update_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	//TODO: Verify update was successfull
	$responseData = create_header("Success", "Device updated", "update_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
    }
}
$responseData = create_header("ERROR", "Unknown error occured", "update_device", "");
log_activity($dblink, $responseData);
echo $responseData;
$dblink->close();
die();
?>