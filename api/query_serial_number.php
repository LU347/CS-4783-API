<?php
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "query_serial_number", "");
	echo $responseData;
	die();
}

$method_array = ['get_auto_id', 'check_duplicates'];

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Serial Number is invalid or missing", "query_serial_number", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!($is_clean = check_serial_format($serial_number))) {
	$responseData = create_header("ERROR", "Invalid serial number", "query_serial_number", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($method == NULL)
{
	$responseData = create_header("ERROR", "Must specify method", "query_serial_number", "");
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

if (strcmp($method, "get_auto_id") === 0)
{
	$sql = "SELECT auto_id FROM serial_numbers WHERE serial_number = " . "'" . $serial_number . "'" ;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_serial_number", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}

	if ($result->num_rows === 0)
	{
		$responseData = create_header("ERROR", "No auto_id associated with the given serial number", "query_serial_number", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$auto_id = $resultArray['auto_id'];
		$responseData = create_header("Success", "Auto_id of serial number found", "query_serial_number", $auto_id);
		echo $responseData;
		die();
	}
}

if (strcmp($method, "check_duplicates") == 0)
{
	$sql = "SELECT auto_id FROM serial_numbers WHERE serial_number = " . "'" . $serial_number . "'" ;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with sql: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "query_serial_number", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}

	if ($result->num_rows === 0)
	{
		$responseData = create_header("Success", "No duplicates found", "query_serial_number", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$auto_id = $resultArray['auto_id'];
		$responseData = create_header("ERROR", "Duplicate serial number found", "query_serial_number", $auto_id);
		echo $responseData;
		die();
	}
}
$responseData = create_header("ERROR", "Unknown Error occured", "query_serial_number", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>