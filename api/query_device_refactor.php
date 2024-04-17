<?php
/*
	Required parameters per method:
	get_device_id requires a valid device type [done]
	get_device_type requires a valid device id [done]
	check_duplicates requires a valid device id [done]
	check_status requires a valid device id [done]
	TODO:
	get devices depending on $status = "ACTIVE" || "INACTIVE"
*/
$dblink = db_connect("equipment");
//TODO: Prevent SQL Injection
//TODO: Redo logging
$method_array = ['get_device_id', 'get_device_type', 'check_duplicates', 'check_status'];

if ($method == NULL)
{
	$responseData = create_header("ERROR", "Method of query required", "query_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (ctype_digit($method) == true) {
	$responseData = create_header("ERROR", "Method contains special characters or numbers", "query_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!in_array($method, $method_array)) {
	$responseData = create_header("ERROR", "Invalid Method", "query_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($device_id == NULL)
{
	$responseData = create_header("ERROR", "Device id given is invalid or missing", "query_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (ctype_digit($device_id) == false || !ctype_alnum($device_id)) {
	$responseData = create_header("ERROR", "Device id contains special characters or is not numeric", "query_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if (strcmp($method, "get_device_id") === 0)
{
	if ($device_type == NULL)
	{
		$responseData = create_header("ERROR", "Device type is missing", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (ctype_digit($device_type) == true || !ctype_alnum($device_type)) {
		$responseData = create_header("ERROR", "Device type contains special characters or numbers", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (preg_match('~[0-9]+~', $device_type)) {
		$responseData = create_header("ERROR", "Invalid Device type: contains numbers", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "SELECT auto_id FROM devices WHERE device_type = " . "'" . $device_type . "'";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($data = $result->fetch_assoc()) //since i'm only expecting one row
	{
		$output = $data['auto_id'];
		$responseData = create_header("Success", "Device ID Found", "query_device", $output);
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (is_null($data = $result->fetch_assoc())) {
		$responseData = create_header("ERROR", "MySQL returned NULL or Device ID was not found", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}

if (strcmp($method, "get_device_type") === 0)
{
	$sql = "SELECT device_type FROM devices WHERE auto_id = '" . $device_id . "'";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($data = $result->fetch_assoc())
	{
		$output = $data['device_type'];
		$responseData = create_header("Success", "Device type found", "query_device", $output);
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (is_null($data = $result->fetch_assoc())) {
		$responseData = create_header("ERROR", "MySQL returned NULL or Device Type was not found", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}

if (strcmp($method, "check_duplicates") === 0) 
{
	$sql = "SELECT auto_id FROM devices WHERE auto_id = '" . $device_id . "'";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($data = $result->fetch_assoc())
	{
		$responseData = create_header("ERROR", "Duplicate device id found", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (is_null($data = $result->fetch_assoc())) {
		$responseData = create_header("Success", "No duplicates found", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}

if (strcmp($method, "check_status") == 0)
{
	$sql = "SELECT auto_id FROM devices WHERE status = 'ACTIVE' AND auto_id=" . "'" . $device_id . "'";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($data = $result->fetch_assoc())
	{
		$output = $data['auto_id'];
		$responseData = create_header("Success", "Active Device ID found", "query_device", $output);
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (is_null($data = $result->fetch_assoc())) {
		$responseData = create_header("ERROR", "No active devices found with the given device_id", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
}
$responseData = create_header("ERROR", "Unknown Error occured", "query_device", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>