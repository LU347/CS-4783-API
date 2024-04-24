<?php
if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Missing serial number ID", "add_equipment", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!($is_clean = check_serial_format($serial_number))) {
	$responseData = create_header("ERROR", "Invalid serial number", "add_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//Preserves the uppercase of SN and changes the rest of the string into lowercase
//$serial_number = format_serial($serial_number);

if ($is_clean = check_serial_format($serial_number))
{
	$sql = "
    SELECT device_type, manufacturer, serial_number
    FROM serial_numbers
    INNER JOIN manufacturers
    INNER JOIN devices
    ON serial_numbers.manufacturer_id = manufacturers.auto_id 
    AND serial_numbers.device_id = devices.auto_id
    AND serial_number LIKE '$serial_number'
    AND manufacturers.status = 'ACTIVE' AND devices.status = 'ACTIVE'
    LIMIT 1000
";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "ERROR with sql", "search_serial_number", "");
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
		$responseData = create_header("Success", "Found $rows_found row(s)", "search_serial_number", json_encode($payload));
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$responseData = create_header("ERROR", "No results found", "search_serial_number", "");
		log_activity($dblink, $responseData);
        echo $responseData;
        die();
	}  
}
?>