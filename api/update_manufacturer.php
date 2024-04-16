<?php
/*
$manufacturer_id = $_REQUEST['manufacturer_id'];
$updated_str = $_REQUEST['updated_str'];
*/
$dblink = db_connect("equipment");

if ($manufacturer_id == NULL)
{
	$responseData = create_header("ERROR", "Manufacturer ID missing or invalid", "update_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($updated_str == NULL)
{
	$responseData = create_header("ERROR", "Missing new manufacturer", "update_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} else if (ctype_digit($updated_str) == true)
{
	$responseData = create_header("ERROR", "New manufacturer is not a string", "update_device", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//need to query_manufacturer to see if the manufacturer id is valid, then update the manufacturer id with the updated str
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?method=get_manufacturer&manufacturer_id=" . $manufacturer_id;
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

//need to query_manufacturer to check if the updated string value already exists in the database
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?method=check_manufacturer_duplicate&manufacturer_id=" . $updated_str;
$result = call_api($url);
$resultsArray = json_decode($result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));

if (strcmp($status, "ERROR") == 0) //means the updated manufacturer already exists
{
	$responseData = create_header("ERROR", "The manufacturer you are trying to update to is already in the database", "query_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//if success that means the updated str does not exist in the database
//now i can update the manufacturer_id with updated_str
if (strcmp($status, "Success") == 0)
{
	//i can update the auto id with the new str
	$sql = "UPDATE manufacturers SET manufacturer ='" . ucfirst($updated_str) . "' WHERE auto_id=" . $manufacturer_id;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql: $e", "update_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$responseData = create_header("Success", "Device updated", "update_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}
$dblink->close();
die();
?>