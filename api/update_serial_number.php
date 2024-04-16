<?php
/*
$serial_number = $_REQUEST['serial_number'];
$updated_str = $_REQUEST['updated_str'];
*/
$dblink = db_connect("equipment");
if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Serial number missing or invalid", "update_serial_number", "");
	echo $responseData;
	die();
} 

if ($updated_str == NULL)
{
	$responseData = create_header("ERROR", "Missing new serial number", "update_serial_number", "");
	echo $responseData;
	die();
} else if (ctype_digit($updated_str) == true) { //input is fully numeric
	$responseData = create_header("ERROR", "Invalid serial number, follow the format", "update_serial_number", "");
	echo $responseData;
	die();
}

//need to query_serial_number to see if the serial number is valid, then update the serial number with the updated str
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_serial_number?serial_number=" . $serial_number; //returns an error if the serial number exists

$result = call_api($url);
$resultsArray = json_decode($result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));
$auto_id = -1;

if (strcmp($status, "Success") == 0) //serial number does not exist
{
	$responseData = create_header("ERROR", "Serial number does not exist", "query_serial_number", "");
	echo $responseData;
	die();
}

if (strcmp($status, "ERROR") == 0) //serial number is in db
{
	//get auto id of $serial_number
	$auto_id = substr($resultsArray[3], 5);
}

//need to query_serial_number to check if the updated string value already exists in the database
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_serial_number?serial_number=" . $updated_str;
$result = call_api($url);
$resultsArray = json_decode($result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));

if (strcmp($status, "ERROR") == 0) //means the updated serial already exists
{
	$responseData = create_header("ERROR", "The serial number you are trying to update to is already in the database", "query_serial_number", "");
	echo $responseData;
	die();
}

//if success that means the updated str does not exist in the database
//now i can update the serial_number with updated_str
if (strcmp($status, "Success") == 0)
{
	//i can update the auto id with the new serial number
	$sql = "UPDATE serial_numbers SET serial_number ='" . strtoupper($updated_str) . "' WHERE auto_id=" . $auto_id;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql: $e", "update_serial_number", "");
		echo $responseData;
		die();
	}
	
	$responseData = create_header("Success", "Device updated", "update_serial_number", "");
	echo $responseData;
	die();
}
$dblink->close();
die();
?>