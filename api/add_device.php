<?php
$dblink = db_connect("equipment");

$device_type = trim(urldecode($device_type));

if ($device_type == NULL)
{
    $responseData = create_header("ERROR", "Device type is missing", "add_device", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
} elseif (!($is_clean = check_device_format($device_type))) {
	$responseData = create_header("ERROR", "Invalid device format", "add_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

$encoded_device = urlencode($device_type);
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?method=check_duplicates&device_type=" . $encoded_device;
$result = call_api($url);
$resultsArray = json_decode($result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

if (strcmp($status, "ERROR") == 0) //Duplicate found
{
	$responseData = create_header("ERROR", $msg, "query_device", "");
	log_activity($dblink, $responseData);
    echo $responseData;
	die();
} 

if (strcmp($status, "Success") == 0) // No duplicates were found
{
	$device_type = strtolower($device_type);
	$sql = "INSERT INTO devices (device_type, status) 
	VALUES ('$device_type', 'ACTIVE')";
	
	try {
		$result = $dblink->query($sql);
	} catch(Exception $e) {
		$errorMsg = "Error with SQL" . $e;
		$responseData = create_header("ERROR", $errorMsg, "add_device", "");
		log_activity($dblink, $responseData);
    	echo $responseData;
		die();
	}
	
	//Need to check if it was inserted properly somehow
	$responseData = create_header("Success", "Device added successfully", "query_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}
$responseData = create_header("ERROR", "Unknown Error occured", "add_device", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>
