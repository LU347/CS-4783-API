<?php
//need to refactor
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "search_equipment", "");
	echo $responseData;
	die();
}

$search_methods = ['device', 'manufacturer', 'serial', 'all'];

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
		include("search_manufacturer.php");
		break;
	case "serial":
		include("search_serial_number.php");
		break;
	case "all":
		break;
	default:
		break;
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