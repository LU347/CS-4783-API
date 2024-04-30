<?php
$dblink = db_connect("equipment");
if (!$dblink) {
	$responseData = create_header("ERROR", "ERROR connecting to database", "new_search", "");
	echo $responseData;
	die();
}

if ($device_id && (!$manufacturer_id && !$serial_number)) {
	//search by device
	if (!$valid = validate_int($device_id)) {
		$responseData = create_header("ERROR", "Invalid device ID format", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if (strcmp($status, "ACTIVE") === 0)
	{
		$is_active = query_device($device_id);
		if (!$is_active) {
			$responseData = create_header("ERROR", "Inactive device ID", "new_search", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		$limiter = " AND manufacturers.status = 'ACTIVE'";
	}
	
    if (strcmp($status, "INACTIVE") === 0) {
		$limiter = " AND manufacturers.status = 'INACTIVE'";
	}
	
	if (strcmp($status, "BOTH") === 0) {
		$limiter = " AND (manufacturers.status = 'INACTIVE' OR manufacturers.status = 'ACTIVE')";
	}
	
	$sql = "
		SELECT manufacturers.status AS manufacturer_status, devices.status AS device_status, devices.device_type, manufacturers.manufacturer, serial_numbers.serial_number
		FROM serial_numbers
		INNER JOIN manufacturers ON serial_numbers.manufacturer_id = manufacturers.auto_id
		INNER JOIN devices ON serial_numbers.device_id = devices.auto_id
		WHERE (serial_numbers.device_id = $device_id)
			$limiter
		LIMIT 1000;
	";
	
} 

if ($manufacturer_id && (!$device_id && !$serial_number)) {
	//search by manufacturer
	if (!$valid = validate_int($manufacturer_id)) {
		$responseData = create_header("ERROR", "Invalid manufactrer ID format", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if (strcmp($status, "ACTIVE") === 0)
	{
		$is_active = query_manufacturer($manufacturer_id);
		if (!$is_active) {
			$responseData = create_header("ERROR", "Inactive manufacturer ID", "new_search", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		$limiter = " AND devices.status = 'ACTIVE'";
	}
	
	if (strcmp($status, "INACTIVE") === 0) {
		$limiter = " AND devices.status = 'INACTIVE'";
	}
	
	if (strcmp($status, "BOTH") === 0) {
		$limiter = " AND (devices.status = 'INACTIVE' OR devices.status = 'ACTIVE')";
	}
	
	$sql = "
		SELECT manufacturers.status AS manufacturer_status , devices.status AS device_status, devices.device_type, manufacturers.manufacturer, serial_numbers.serial_number
		FROM serial_numbers
		INNER JOIN manufacturers ON serial_numbers.manufacturer_id = manufacturers.auto_id
		INNER JOIN devices ON serial_numbers.device_id = devices.auto_id
		WHERE (serial_numbers.manufacturer_id = $manufacturer_id AND manufacturers.auto_id = $manufacturer_id)
			$limiter
		LIMIT 1000;
	"; 
}

if ($serial_number && (!$device_id && !$manufacturer_id)) {
	//search by serial
	if ($serial_number === NULL) {
		$responseData = create_header("ERROR", "Invalid serial number format", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (!($is_clean = check_serial_format($serial_number))) {
		$responseData = create_header("ERROR", "Invalid serial number", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
		SELECT manufacturers.status AS manufacturer_status , devices.status AS device_status, devices.device_type, manufacturers.manufacturer, serial_numbers.serial_number
		FROM serial_numbers
		INNER JOIN manufacturers ON serial_numbers.manufacturer_id = manufacturers.auto_id
		INNER JOIN devices ON serial_numbers.device_id = devices.auto_id
		WHERE serial_numbers.serial_number LIKE '%$serial_number'
			$limiter
		LIMIT 1000;
	";  
}

if ($device_id && $manufacturer_id && $serial_number) {
	//search all
	$device_valid = validate_int($device_id);
	$manu_valid = validate_int($manufacturer_id);
	if ($device_valid === false || $manu_valid === false) {
		$responseData = create_header("ERROR", "Invalid device or manufacturer ID format", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	if (strcmp($status, "ACTIVE") === 0)
	{
		$is_active = query_device($device_id);
		if (!$is_active) {
			$responseData = create_header("ERROR", "Inactive device ID", "new_search", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
	}
	
	if ($serial_number === NULL) {
		$responseData = create_header("ERROR", "Invalid serial number format", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} elseif (!($is_clean = check_serial_format($serial_number))) {
		$responseData = create_header("ERROR", "Invalid serial number", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
		SELECT manufacturers.status AS manufacturer_status , devices.status AS device_status, devices.device_type, manufacturers.manufacturer, serial_numbers.serial_number
		FROM serial_numbers
		INNER JOIN manufacturers ON serial_numbers.manufacturer_id = manufacturers.auto_id
		INNER JOIN devices ON serial_numbers.device_id = devices.auto_id
		WHERE serial_numbers.serial_number LIKE '%$serial_number'
			AND serial_numbers.manufacturer_id = $manufacturer_id
			AND serial_numbers.device_id = $device_id
			$limiter
		LIMIT 1000;
	";
}

//Run SQL Statement
if (!empty($sql))
{
		try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql $e", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}

	$rows_found = $result->num_rows;
	if ($rows_found > 0)
	{

		while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
		{
			//row = status, device_type, manufacturer, serial_number
			$manu_status = $equipment_data['manufacturer_status'];
			$device_status = $equipment_data['device_status'];
			$row = $manu_status . "," . $device_status . "," . $equipment_data['device_type'] . "," . $equipment_data['manufacturer'] . "," . $equipment_data['serial_number'];
			$payload[] = $row;
		}	
		$responseData = create_header("Success", "Found $rows_found row(s)", "new_search", json_encode($payload));
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$responseData = create_header("ERROR", "No results found", "new_search", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}  
}
	
$responseData = create_header("ERROR", "Invalid or missing search method", "new_search", "");
log_activity($dblink, $responseData);
echo $responseData;
die();

?>