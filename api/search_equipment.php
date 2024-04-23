<?php
//need to refactor
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "search_equipment", "");
	echo $responseData;
	die();
}

$search_methods = ['device', 'manufacturer', 'serial_number', 'all'];

if ($search_by == NULL)
{
	$responseData = create_header("ERROR", "Invalid search condition", "search_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!($is_clean = check_string_format($search_by))) {
	$responseData = create_header("ERROR", "Invalid search condition format", "search_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!in_array($search_by, $search_methods)) {
	$responseData = create_header("ERROR", "Invalid search method", "search_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

switch($search_by)
{
	case "device":
		include("search_device.php");
		break;
	case "manufacturer":
		break;
	case "serial_number":
		break;
	case "all":
		break;
	default:
		break;
}


//Check if device or manufacturer is valid
if ($search_by == "manufacturer")
{
	if ($manufacturer_id == NULL)
	{
		$responseData = create_header("ERROR", "Manufacturer ID invalid or missing", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
		SELECT device_type, manufacturer, serial_number
		FROM serial_numbers
		INNER JOIN manufacturers
		INNER JOIN devices
		ON serial_numbers.manufacturer_id = manufacturers.auto_id 
		AND serial_numbers.device_id = devices.auto_id
		AND manufacturers.auto_id = $manufacturer_id
		AND manufacturers.status = 'ACTIVE' AND devices.status = 'ACTIVE'
		LIMIT 1000
	";
	
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("ERROR", "No results found", "search_equipment", "");
		echo $responseData;
		die();
	}
	
	//need to check if the sql result returned > 0 rows
	while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
	{
		$row = $equipment_data['device_type'] . "," . $equipment_data['manufacturer'] . "," . $equipment_data['serial_number'];
		$payload[] = $row;
	}
	$responseData = create_header("Success", "Search by manufacturer success", "search_equipment", json_encode($payload));
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
	
}

if ($search_by == "serial_number")
{
	if ($serial_number == NULL)
	{
		$responseData = create_header("ERROR", "Serial Number invalid or missing", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
		
	//i will need the manufacturer name and device type this time
	//check if serial_number is valid?
	$sql = "SELECT * FROM serial_numbers WHERE serial_number LIKE '%" . $serial_number . "' LIMIT 10000";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_equipment", "");
		log_activity($dblink, $responseData);
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
			log_activity($dblink, $responseData);
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
			log_activity($dblink, $responseData);
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
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	if ($manufacturer_id == NULL)
	{
		$responseData = create_header("ERROR", "Manufacturer id invalid or missing", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	if ($serial_number == NULL)
	{
		$responseData = create_header("ERROR", "Serial Number invalid or missing", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
        SELECT device_type, manufacturer, serial_number 
        FROM serial_numbers 
        INNER JOIN manufacturers 
        INNER JOIN devices 
        ON serial_numbers.manufacturer_id = manufacturers.auto_id 
        AND serial_numbers.device_id = devices.auto_id 
        AND manufacturers.status = 'ACTIVE' 
        AND devices.status = 'ACTIVE' 
        LIMIT 1000";
	
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("Error", "No results found", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		$result->close();
		die();
	}
	
	while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
	{
		//need to pull device type and manufacturer name
		$row = $equipment_data['device_type'] . "," . $equipment_data['manufacturer'] . "," . $equipment_data['serial_number'];
		$payload[] = $row;
	}
    $responseData = create_header("Success", "Search by device success", "search_equipment", json_encode($payload));
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}
$responseData = create_header("ERROR", "Unknown Error occured", "search_equipment", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>