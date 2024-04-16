<?php
//need to refactor
$dblink = db_connect("equipment");
$sql = "SELECT `auto_id` FROM `serial_numbers` WHERE `serial_number` = '$serial_number'";

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Serial Number is invalid or missing", "query_serial_number", "");
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
}

if (strcmp($method, "get_auto_id") == 0)
{
	$sql = "SELECT auto_id FROM serial_numbers WHERE serial_number =" . $serial_number ;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql: $e", "query_serial_number", "");
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
	} else if (is_null($result->fetch_array(MYSQLI_ASSOC)))
	{
		$responseData = create_header("ERROR", "MYSQL returned null", "query_serial_number", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		//Needs testing
		$data = $result->fetch_array(MYSQLI_ASSOC);
		$jsonData = json_encode($data);
		echo $jsonData;
		die();
		$responseData = create_header("Success", "Auto_id of serial number found", "query_serial_number", "");
	}
}

if (strcmp($method, "check_duplicate") == 0)
{
	$sql = "SELECT auto_id FROM serial_numbers WHERE serial_number =" . $serial_number ;
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql: $e", "query_serial_number", "");
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
	} else if (is_null($result->fetch_array(MYSQLI_ASSOC)))
	{
		//needs testing
		$responseData = create_header("ERROR", "MYSQL returned null", "query_serial_number", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		//Needs testing
		$data = $result->fetch_array(MYSQLI_ASSOC);
		$jsonData = json_encode($data);
		echo $jsonData;
		die();
		$responseData = create_header("ERROR", "Duplicate Serial Number", "query_serial_number", $jsonData);
	}
}
$dblink->close();
die();
?>