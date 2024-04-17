<?php
$dblink = db_connect("equipment");
if (strcmp($method, "get_device_type") == 0 )
{
	$sql = "SELECT device_type FROM devices WHERE auto_id = $device_id";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR, Error with sql", "query_device", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("Error", "Device type does not exist", "query_device", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		$dblink->close();
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$device_type = $resultArray['device_type'];
		$responseData = create_header("Success", "Device type successfully found", "query_device", $device_type);
		echo $responseData;
		log_activity($dblink, $responseData);
		$dblink->close();
		die();
	}
} elseif (strcmp($method, "check_device_duplicate") == 0) {
	//TODO: need to check by device_type too
	$sql = "SELECT auto_id FROM devices WHERE device_type = '$device_id'"; // should be device type
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR, Error with sql", "query_device", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("Success", "Device type does not exist", "query_device", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		$dblink->close();
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$auto_id = $resultArray['auto_id'];
		$responseData = create_header("ERROR", "Device type already exists", "query_device", $auto_id);
		echo $responseData;
		log_activity($dblink, $responseData);
		$dblink->close();
		die();
	}
}

?>