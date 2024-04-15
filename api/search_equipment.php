<?php
//need to refactor
if ($search_by == NULL)
{
	$responseData = create_header("ERROR", "Invalid Search Condition", "search_equipment", "");
	echo $responseData;
	die();
}

//Check if device or manufacturer is valid
if ($search_by == "device")
{
	//query device is used to get the device_id, i already have the device_id
	if ($device_id == NULL)
	{
		$responseData = create_header("ERROR", "Device ID invalid or missing", "search_equipment", "");
		echo $responseData;
		die();
	}
	//need check if device id is valid
	//method=get_device_type gets the device type and requires the device auto id
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?device_id=" . $device_id . "&method=get_device_type";

    $result = call_api($url);
	$resultsArray = json_decode($result, true);
	$status = trim(get_msg_status($resultsArray));
	$device_type = "";
	
	//device type was successfully found
	if (strcmp($status, "Success") == 0)
	{
		$device_type = trim(substr($resultsArray[3], 5)); //only expecting the device id
	}
	
	//device type wasn't found
	if (strcmp($status, "ERROR") == 0)
	{
		$responseData = create_header("ERROR", "Device type no found", "query_device", "");
		echo $responseData;
		die();
	}
	
	$sql = "SELECT auto_id, manufacturer_id, serial_number FROM serial_numbers WHERE device_id =" . $device_id . " LIMIT 10000";
	
	$dblink = db_connect("equipment"); //move
	
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_equipment", "");
		echo $responseData;
		die();
	}
	
	while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
	{
		//need to get manufacturer type per row so each row looks like:
		//hide auto id?
		//auto_id, computer, apple, serialnumber
		$manufacturer = "";
		$manu_sql = "SELECT manufacturer FROM manufacturers WHERE auto_id =".$equipment_data['manufacturer_id'];
		try {
			$manu_query_result = $dblink->query($manu_sql);
			$manu_data = $manu_query_result->fetch_array(MYSQLI_ASSOC);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "search_equipment", "");
			echo $responseData;
			die();
		}
		$row = $device_type . "," . $manu_data['manufacturer'] . "," . $equipment_data['serial_number'];
		$payload[$equipment_data['auto_id']] = $row;
	}	
	$responseData = create_header("Success", "Search by device success", "search_equipment", json_encode($payload));
	echo $responseData;
	die();
}

if ($search_by == "manufacturer")
{
	if ($manufacturer_id == NULL)
	{
		$responseData = create_header("ERROR", "Manufacturer ID invalid or missing", "search_equipment", "");
		echo $responseData;
		die();
	}
	//query manufacturer to check if the id is valid and to get the manufacturer name
	//method = get_manufacturer
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?manufacturer_id=" . $manufacturer_id . "&method=get_manufacturer";
	
	$result = call_api($url);
	$resultsArray = json_decode($result, true);
	$status = trim(get_msg_status($resultsArray));
	$manufacturer = "";
	
	//if the manufacturer id was found, the manufacter name is returned
	if (strcmp($status, "Success") == 0)
	{
		$manufacturer = trim(substr($resultsArray[3], 5));
	}
	
	if (strcmp($status, "ERROR") == 0)
	{
		$responseData = create_header("ERROR", "Manufacturer not found in database", "query_manufacturer", "");
		echo $responseData;
		die();
	}
	
	$sql = "SELECT auto_id, device_id, serial_number FROM serial_numbers WHERE manufacturer_id=" . $manufacturer_id .  " LIMIT 10000";
	$dblink = db_connect("equipment");
	
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_equipment", "");
		echo $responseData;
		die();
	}
	
	//need to check if the sql result returned > 0 rows
	while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
	{
		//need to pull the device type instead of manufacturer this time
		//format auto_id, device type, manufacturer name, serial number
		$device_type = "";
		$device_sql = "SELECT device_type FROM devices WHERE auto_id=" . $equipment_data['device_id'];
		try {
			$device_sql_result = $dblink->query($device_sql);
			$device_data = $device_sql_result->fetch_array(MYSQLI_ASSOC);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "search_equipment", "");
			echo $responseData;
			die();
		}
		$row = $equipment_data['device_type'] . "," . $manufacturer . "," . $equipment_data['serial_number'];
		$payload[$equipment_data['auto_id']] = $row;
	}
	$responseData = create_header("Success", "Search by manufacturer success", "search_equipment", json_encode($payload));
	echo $responseData;
	die();
	
}

if ($search_by == "serial_number")
{
	if ($serial_number == NULL)
	{
		$responseData = create_header("ERROR", "Serial Number invalid or missing", "search_equipment", "");
		echo $responseData;
		die();
	}
		
	//i will need the manufacturer name and device type this time
	//check if serial_number is valid?
	$sql = "SELECT * FROM serial_numbers WHERE serial_number LIKE '%" . $serial_number . "' LIMIT 10000";
	$dblink = db_connect("equipment");
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_equipment", "");
		echo $responseData;
		die();
	}
	
	//check if there are no results
	while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
	{
		//need to pull device type and manufacturer name
		$device_type = "";
		$device_sql = "SELECT device_type FROM devices WHERE auto_id=" . $equipment_data['device_id'];
		try {
			$device_sql_result = $dblink->query($device_sql);
			$device_data = $device_sql_result->fetch_array(MYSQLI_ASSOC);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "search_equipment", "");
			echo $responseData;
			die();
		}
		$device_type = $device_data['device_type'];
		
		$manufacturer = "";
		$manu_sql = "SELECT manufacturer FROM manufacturers WHERE auto_id =".$equipment_data['manufacturer_id'];
		try {
			$manu_query_result = $dblink->query($manu_sql);
			$manu_data = $manu_query_result->fetch_array(MYSQLI_ASSOC);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "search_equipment", "");
			echo $responseData;
			die();
		}
		$manufacturer = $manu_data['manufacturer'];
		
		$row = $device_type . "," . $manufacturer . "," . $equipment_data['serial_number'];
		$payload[$equipment_data['auto_id']] = $row;
	}
	$responseData = create_header("Success", "Search by device success", "search_equipment", json_encode($payload));
	echo $responseData;
	die();
}

if ($search_by == "all")
{
	if ($device_id == NULL)
	{
		$responseData = create_header("ERROR", "Device id invalid or missing", "search_equipment", "");
		echo $responseData;
		die();
	}
	if ($manufacturer_id == NULL)
	{
		$responseData = create_header("ERROR", "Manufacturer id invalid or missing", "search_equipment", "");
		echo $responseData;
		die();
	}
	if ($serial_number == NULL)
	{
		$responseData = create_header("ERROR", "Serial Number invalid or missing", "search_equipment", "");
		echo $responseData;
		die();
	}
	
	/*query device */
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?device_id=" . $device_id . "&method=get_device_type";

    $result = call_api($url);
	$resultsArray = json_decode($result, true);
	$status = trim(get_msg_status($resultsArray));
	$device_type = "";
	
	//device type was successfully found
	if (strcmp($status, "Success") == 0)
	{
		$device_type = trim(substr($resultsArray[3], 5)); //only expecting the device id
	}
	
	//device type wasn't found
	if (strcmp($status, "ERROR") == 0)
	{
		$responseData = create_header("ERROR", "Device type no found", "query_device", "");
		echo $responseData;
		die();
	}
	
	/*query manufacturer*/
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?manufacturer_id=" . $manufacturer_id . "&method=get_manufacturer";
	
	$result = call_api($url);
	$resultsArray = json_decode($result, true);
	$status = trim(get_msg_status($resultsArray));
	$manufacturer = "";
	
	//if the manufacturer id was found, the manufacter name is returned
	if (strcmp($status, "Success") == 0)
	{
		$manufacturer = trim(substr($resultsArray[3], 5));
	}
	
	if (strcmp($status, "ERROR") == 0)
	{
		$responseData = create_header("ERROR", "Manufacturer not found in database", "query_manufacturer", "");
		echo $responseData;
		die();
	}
	
	$sql = "SELECT * FROM serial_numbers WHERE device_id = " . $device_id . " AND manufacturer_id = " . $manufacturer_id . " AND serial_number LIKE '" . $serial_number . "' LIMIT 10000";
	
	$dblink = db_connect("equipment");
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_equipment", "");
		echo $responseData;
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("Error", "No results found", "search_equipment", "");
		echo $responseData;
		$result->close();
		die();
	}
	
	while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
	{
		//need to pull device type and manufacturer name
		$device_type = "";
		$device_sql = "SELECT device_type FROM devices WHERE auto_id=" . $equipment_data['device_id'];
		try {
			$device_sql_result = $dblink->query($device_sql);
			$device_data = $device_sql_result->fetch_array(MYSQLI_ASSOC);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "search_equipment", "");
			echo $responseData;
			die();
		}
		$device_type = $device_data['device_type'];
		
		$manufacturer = "";
		$manu_sql = "SELECT manufacturer FROM manufacturers WHERE auto_id =".$equipment_data['manufacturer_id'];
		try {
			$manu_query_result = $dblink->query($manu_sql);
			$manu_data = $manu_query_result->fetch_array(MYSQLI_ASSOC);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "search_equipment", "");
			echo $responseData;
			die();
		}
		$manufacturer = $manu_data['manufacturer'];
		
		$row = $device_type . "," . $manufacturer . "," . $equipment_data['serial_number'];
		$payload[$equipment_data['auto_id']] = $row;
	}
    $responseData = create_header("Success", "Search by device success", "search_equipment", json_encode($payload));
	echo $responseData;
	die();
}
$dblink->close();
die();
?>