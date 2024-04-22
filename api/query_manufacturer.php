<?php
/*
	Required parameters per method:
	get_manufacturer needs $manufacturer_id
	check_status needs $manufacturer_id
	
	check_manufacturer_duplicate needs $manufacturer
	
	TODO:
	get manufacturer depending on $status = "ACTIVE" || "INACTIVE"
*/
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "query_manufacturer", "");
	echo $responseData;
	die();
}

$method_array = ['get_manufacturer', 'check_manufacturer_duplicate', 'check_status'];

if ($method == NULL)
{
	$responseData = create_header("ERROR", "Query method is missing", "query_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (ctype_digit($method)) {
	$responseData = create_header("ERROR", "Method contains special characters or numbers", "query_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!in_array($method, $method_array)) {
	$responseData = create_header("ERROR", "Invalid Method", "query_manufacturer", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//Checking if $manufacturer_id is valid
if (strcmp($method, "get_manufacturer") == 0 || strcmp($method, "check_status") == 0) 
{
	if ($manufacturer_id == NULL) {
		$responseData = create_header("ERROR", "Manufacturer ID is missing", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (!ctype_digit($manufacturer_id)) {
		$responseData = create_header("ERROR", "Manufacturer ID is invalid", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}

//Checking if $manufacturer is a valid string
if (strcmp($method, "check_manufacturer_duplicate") == 0) 
{
	if ($manufacturer == NULL) {
		$responseData = create_header("ERROR", "Manufacturer name is missing", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (!($is_clean = check_string_format($manufacturer))) {
		$responseData = create_header("ERROR", "Invalid manufacturer format", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$manufacturer = urldecode($manufacturer);
}

if (strcmp($method, "get_manufacturer") == 0) 
{	
	$sql = "SELECT manufacturer FROM manufacturers WHERE auto_id = $manufacturer_id";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "ERROR with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($result->num_rows == 0) {
		$responseData = create_header("ERROR", "Manufacturer does not exist", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$manufacturer = $resultArray['manufacturer'];
		$responseData = create_header("Success", "Manufacturer successfully found", "query_manufacturer", $manufacturer);
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}

if (strcmp($method, "check_status") == 0)
{
	$sql = "SELECT auto_id FROM manufacturers WHERE status='ACTIVE' AND auto_id =" . $manufacturer_id;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "ERROR with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($result->num_rows > 0) {
		$responseData = create_header("Success", "Manufacturer found and active", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$responseData = create_header("ERROR", "Manufacturer could not be found or inactive", "query_manufacturer", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

if (strcmp($method, "check_manufacturer_duplicate") == 0) 
{	
	$sql = "SELECT auto_id FROM manufacturers WHERE manufacturer = '$manufacturer'";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR, Error with sql: $e", "query_manufacturer", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("Success", "Manufacturer type does not exist", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$auto_id = $resultArray['auto_id'];
		$responseData = create_header("ERROR", "Manufacturer already exists", "query_device", $auto_id);
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}
$responseData = create_header("ERROR", "Unknown Error occured", "query_manufacturer", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>