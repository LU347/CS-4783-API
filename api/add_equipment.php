<?php
$dblink = db_connect("equipment");
if (!$dblink) 
{
	$responseData = create_header("ERROR", "Failed to connect to database", "add_equipment", "");
	echo $responseData;
	die();
}

if ($device_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "add_equipment", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!ctype_digit($device_id)) {
		$responseData = create_header("ERROR", "Device ID is not numeric", "query_device", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
}

if ($manufacturer_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "query_manufacturer", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!ctype_digit($manufacturer_id)) {
		$responseData = create_header("ERROR", "Manufacturer ID is not numeric", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
}

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Missing serial number ID", "add_equipment", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $serial_number)) {
    $responseData = create_header("ERROR", "Invalid serial number", "add_equipment", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!preg_match('/SN-[\d\w]+/', $serial_number)) {
	$responseData = create_header("ERROR", "Invalid serial format", "add_equipment", "");
	echo $responseData;
	log_activity($dblink, $responseData);
	die();
}

//check if device_id is valid and active
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?method=check_status&device_id=" . $device_id;
$results = call_api($url);
$resultsArray = json_decode($results, true);
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

if (strcmp($status, "ERROR") == 0)
{
    $responseData = create_header("ERROR", $msg, "query_device", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
}
//$device_id is valid if it's successful

//check if manufacturer_id is valid and active

$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?method=check_status&manufacturer_id=" . $manufacturer_id;
$results = call_api($url);
$resultsArray = json_decode($results, true);
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

if (strcmp($status, "ERROR") == 0)
{
    $responseData = create_header("ERROR", $msg, "query_manufacturer", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
}
//manufacturer is in db if it's successfull

$serial_number = trim($serial_number);
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_serial_number?method=check_duplicates&serial_number=" . $serial_number;
$results = call_api($url);
$resultsArray = json_decode($results, true);
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

if (strcmp($status, "ERROR") == 0)
{
    $responseData = create_header("ERROR", $msg, "query_serial_number", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
}

if (strcmp($status, "Success") == 0)
{
	$sql = "INSERT INTO serial_numbers (device_id, manufacturer_id, serial_number) 
	VALUES ('$device_id', '$manufacturer_id', '$serial_number')";
	
	try {
		$result = $dblink->query($sql);
	} catch(Exception $e) {
		$errorMsg = "Error with SQL" . $e;
		$responseData = create_header("ERROR", $errorMsg, "add_equipment", "");
    	echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	$responseData = create_header("Success", "Equipment successfully added!", "add_equipment", "");
	echo $responseData;
	log_activity($dblink, $responseData);
	die();
}

$responseData = create_header("ERROR", "Unknown Error occured", "add_equipment", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>