<?php
$dblink = db_connect("equipment");

if ($device_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "query_device", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} else {
	//check if device_id is valid and active
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?device_id=";
} 

if ($manufacturer_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "query_manufacturer", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
}

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Missing serial number ID", "None", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
}

$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_serial_number?serial_number=" . $serial_number . "&method=check_duplicate";
$serial_query_result = call_api($url);

$resultsArray = json_decode($serial_query_result, true); //turns result into array
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
$dblink->close();
?>