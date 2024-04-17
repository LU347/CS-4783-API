<?php
$dblink = db_connect("equipment");

if ($device_type == NULL)
{
    $responseData = create_header("ERROR", "Device type is missing", "query_device", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
} elseif (ctype_digit($device_type) == true) {
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

$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?method=check_duplicates&device_type=" . $device_type;
$result = call_api($url);
$resultsArray = json_decode($result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

if (strcmp($status, "ERROR") == 0) //Duplicate found
{
	$responseData = create_header("ERROR", $msg, "query_device", "");
	log_activity($dblink, $responseData);
	$dblink->close();
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
		$dblink->close();
    	echo $responseData;
		die();
	}

	//Need to check if it was inserted properly somehow
	$responseData = create_header("Success", "Device added successfully", "query_device", "");
	log_activity($dblink, $responseData);
	$dblink->close();
	echo $responseData;
	die();
}
$responseData = create_header("ERROR", "Unknown Error occured", "add_device", "");
log_activity($dblink, $responseData);
$dblink->close();
echo $responseData;
die();
?>
