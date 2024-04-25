<?php
$dblink = db_connect( "equipment" );
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to db", "list_all_equipment", "");
	echo $responseData;
	die();
}

$sql = "
  SELECT devices.device_type, manufacturers.manufacturer, serial_numbers.serial_number 
  FROM serial_numbers 
  INNER JOIN manufacturers ON serial_numbers.manufacturer_id = manufacturers.auto_id 
  INNER JOIN devices ON serial_numbers.device_id = devices.auto_id
  WHERE manufacturers.status = 'ACTIVE' AND devices.status = 'ACTIVE' 
  LIMIT 1000
  ";
$result = "";
$equipment = array();

try {
	$result = $dblink->query($sql);
} catch (Exception $e) {
	$responseData = create_header("ERROR", "Error with sql (fetching devices): $e", "list_devices", "");
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
?>