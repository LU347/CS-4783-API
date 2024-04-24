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
} elseif (!(ctype_digit($device_id))) {
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
} elseif (!(ctype_digit($manufacturer_id))) {
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
} elseif (!($is_clean = check_serial_format($serial_number))) {
	$responseData = create_header("ERROR", "Invalid serial number", "add_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//Preserves the uppercase of SN and changes the rest of the string into lowercase
$serial_number = format_serial($serial_number);

//check if device_id is valid and active
$device_valid = query_device($device_id);
if (!$device_valid)
{
	$responseData = create_header("ERROR", "Invalid device id or inactive", "add_equipment", "");
	echo $responseData;
	log_activity($dblink, $responseData);
	die();
}
//$device_id is valid if it's successful

//check if manufacturer_id is valid and active
$manufacturer_valid = query_manufacturer($manufacturer_id);
if (!$manufacturer_valid)
{
	$responseData = create_header("ERROR", "Invalid manufacturer is or inactive", "query_manufacturer", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
}
//manufacturer is in db if it's successfull

//check if there is an existing serial number already
$serial_available = query_serial_number($serial_number);
if (!$serial_available)
{
	$responseData = create_header("ERROR", "Duplicate serial entry found", "add_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} else {
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