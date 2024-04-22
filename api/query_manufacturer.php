<?php
/*
	Required parameters per method:
	get_manufacturer needs $manufacturer_id
	
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

$method_array = ['get_manufacturer', 'check_manufacturer_duplicate'];

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
if (strcmp($method, "get_manufacturer") == 0) 
{
	if ($manufacturer_id == NULL) {
		$responseData = create_header("ERROR", "Manufacturer ID is missing", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (!ctype_digit($manufactuer_id)) {
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
		$responseData = create_header("ERROR", "Manufacturer name is invalid or missing", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (ctype_digit($manufactuer)) {
		$responseData = create_header("ERROR", "Manufacturer name is fully numeric", "query_manufacturer", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (preg_match('~[0-9]+~', $manufacturer)) {
		$responseData = create_header("ERROR", "Manufacturer name contains numbers", "query_device", "");
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
		$responseData = create_header("ERROR", "Error with sql: $e", "query_manufacturer", "");
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
} elseif (strcmp($method, "check_manufacturer_duplicate") == 0) {	
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
$dblink->close();
echo $responseData;
die();
?>