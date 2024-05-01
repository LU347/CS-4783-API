<?php
/*
https://stackoverflow.com/questions/3938021/how-to-check-for-special-characters-php
*/
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "add_manufacturer", "");
	echo $responseData;
	die();
}

$manufacturer = trim(urldecode($manufacturer));

if ($manufacturer == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing manufacturer", "add_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!($is_clean = check_string_format($manufacturer))) {
	$responseData = create_header("ERROR", "Invalid manufacturer format", "add_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

$encoded_manufacturer = urlencode($manufacturer);
$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?manufacturer=" . $encoded_manufacturer . "&method=check_manufacturer_duplicate";

$result = call_api($url);
$resultsArray = json_decode($result, true);
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1],4));

if (strcmp($status, "ERROR") == 0)
{
	$responseData = create_header("ERROR", $msg, "query_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if (strcmp($status, "Success") == 0)	//Manufacturer wasn't found 
{
	$manufacturer = ucfirst($manufacturer);
	$sql = "INSERT INTO  manufacturers (manufacturer, status)
			VALUES ('$manufacturer', 'ACTIVE')";
	
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with SQL: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "add_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$responseData = create_header("Success", "Manufacturer successfully added!", "add_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}
$responseData = create_header("ERROR", "Unknown Error occured", "add_manufacturer", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>