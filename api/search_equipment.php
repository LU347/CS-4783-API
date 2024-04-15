<?php
/*
	if device_id == null
		person is searching by manufacturer or serial number or just manufacturer
	if manufacturer_id == null
		person is searching by device or serial number
*/

if ($search_by == NULL)
{
	/*
		search by device = device
		search by manufacturer = manufacturer
		search by serial = serial
		search all (device, manufacturer, serial) = all
	*/
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
	
	$sql = "SELECT auto_id, manufacturer_id, serial_number FROM serial_numbers WHERE device_id =" . $device_id . " LIMIT 10";
	
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
die();
?>