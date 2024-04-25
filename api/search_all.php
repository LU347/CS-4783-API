<?php

if ($device_id == NULL)
{
    $responseData = create_header("ERROR", "Device ID invalid or missing", "search_all", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
} elseif (!ctype_digit($device_id)) {
    $responseData = create_header("ERROR", "Device ID is not numeric", "search_all", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

if ($manufacturer_id == NULL)
{
    $responseData = create_header("ERROR", "Manufacturer ID invalid or missing", "search_all", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
} elseif (!ctype_digit($manufacturer_id)) {
    $responseData = create_header("ERROR", "Manufacturer ID is not numeric", "search_all", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Missing serial number ID", "search_all", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!($is_clean = check_serial_format($serial_number))) {
	$responseData = create_header("ERROR", "Invalid serial number", "search_all", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//check if device_id is valid and active
$device_valid = query_device($device_id);
if (!$device_valid)
{
	$responseData = create_header("ERROR", "Invalid device id or inactive", "search_all", "");
	echo $responseData;
	log_activity($dblink, $responseData);
	die();
}
//$device_id is valid if it's successful

//check if manufacturer_id is valid and active
$manufacturer_valid = query_manufacturer($manufacturer_id);
if (!$manufacturer_valid)
{
	$responseData = create_header("ERROR", "Invalid manufacturer is or inactive", "search_all", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
}

if ($is_clean = check_serial_format($serial_number))
{
	$sql = "
 	  SELECT devices.device_type, manufacturers.manufacturer, serial_numbers.serial_number 
      FROM serial_numbers 
      INNER JOIN manufacturers ON serial_numbers.manufacturer_id = manufacturers.auto_id 
      INNER JOIN devices ON serial_numbers.device_id = devices.auto_id
      WHERE serial_numbers.device_id = $device_id
		AND serial_numbers.manufacturer_id = $manufacturer_id
		AND serial_numbers.serial_number LIKE '$serial_number'
		AND manufacturers.status = 'ACTIVE' 
		AND devices.status = 'ACTIVE' 
	  LIMIT 1000
	  ";
	
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("Error", "Error with sql $e", "search_all", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}

	$rows_found = $result->num_rows;
	if ($rows_found > 0)
	{
		while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
    	{
			$row = $equipment_data['device_type'] . "," . $equipment_data['manufacturer'] . "," . $equipment_data['serial_number'];
			$payload[] = $row;
    	}	
		$responseData = create_header("Success", "Found $rows_found row(s)", "search_all", json_encode($payload));
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$responseData = create_header("ERROR", "No results found", "search_all", "");
		log_activity($dblink, $responseData);
        echo $responseData;
        die();
	}  
}

$responseData = create_header("ERROR", "Unknown Error occured", "search_all", "");
log_activity($dblink, $responseData);
echo $responseData;
die();

?>