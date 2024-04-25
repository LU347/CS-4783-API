<?php
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to DB", "update_equipment", "");
	echo $resopnseData;
	die();
}

if ($device_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "update_equipment", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!(ctype_digit($device_id))) {
	$responseData = create_header("ERROR", "Device ID is not numeric", "update_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($manufacturer_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing manufacturer ID", "update_equipment", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!(ctype_digit($manufacturer_id))) {
	$responseData = create_header("ERROR", "Manufacturer ID is not numeric", "update_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Missing serial number", "update_equipment", "");
    echo $responseData;
	log_activity($dblink, $responseData);
	die();
} elseif (!($is_clean = check_serial_format($serial_number))) {
	$responseData = create_header("ERROR", "Invalid serial number", "update_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//check if device_id is valid and active
$device_valid = query_device($device_id);
if (!$device_valid)
{
	$responseData = create_header("ERROR", "Invalid device id or inactive", "update_equipment", "");
	echo $responseData;
	log_activity($dblink, $responseData);
	die();
}
//$device_id is valid if it's successful


//check if manufacturer_id is valid and active
$manufacturer_valid = query_manufacturer($manufacturer_id);
if (!$manufacturer_valid)
{
	$responseData = create_header("ERROR", "Invalid manufacturer is or inactive", "update_equipment", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
}

$sql = "UPDATE serial_numbers SET device_id=" . $device_id . ", manufacturer_id=" . $manufacturer_id . " WHERE serial_number='" . $serial_number . "'";

try {
    $result = $dblink->query($sql);
} catch (Exception $e) {
    $responseData = create_header("ERROR", "Error with sql: $e", "update_equipment", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

$verify_sql = "
    SELECT * FROM serial_numbers 
    WHERE device_id =" . $device_id . " 
    AND manufacturer_id=" . $manufacturer_id . " 
    AND serial_number='" . $serial_number . "'";

try {
    $result = $dblink->query($verify_sql);
} catch (Exception $e) {
    $responseData = create_header("ERROR", "Error with sql: $e", "update_equipment", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

$rows_found = $result->num_rows;
if ($rows_found > 0) {
    $responseData = create_header("Success", "Device updated", "update_equipment", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
} else {
    $responseData = create_header("ERROR", "No equipment updated", "update_equipment", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

$responseData = create_header("ERROR", "Unknown error occured", "update_equipment", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>