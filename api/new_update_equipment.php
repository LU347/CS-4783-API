<?php
/*
	Params
	$device_id, $manufacturer_id, $serial_number (equipment they want to update)
	$new_device, $new_manu, $new_serial	(updated values)
*/
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to DB", "update_equipment", "");
	echo $resopnseData;
	die();
}

if ($new_device && (empty($new_manu) && empty($new_serial))) 
{
	//user only wants to update the equipment's device_id
	if (!$valid = validate_int($new_device)) {
		$responseData = create_header("ERROR", "Invalid device ID format", "update_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
		UPDATE serial_numbers SET serial_numbers.device_id = $new_device
		WHERE serial_numbers.device_id = $device_id
			AND serial_numbers.manufacturer_id = $manufacturer_id
			AND serial_numbers.serial_number = '$serial_number'
	";
		
    $verify_sql = "
        SELECT * FROM serial_numbers
        WHERE serial_numbers.device_id = $new_device
        	AND serial_numbers.manufacturer_id = $manufacturer_id
			AND serial_numbers.serial_number = '$serial_number'
    ";
}

if ($new_manu && (empty($new_device) && empty($new_serial))) 
{
	//user only wants to update the equipment's manufacturer
	if (!$valid = validate_int($new_manu)) {
		$responseData = create_header("ERROR", "Invalid manufacturer ID format", "update_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
		UPDATE serial_numbers SET serial_numbers.manufacturer_id = $new_manu
		WHERE serial_numbers.manufacturer_id = $manufacturer_id
			AND serial_numbers.device_id = $device_id
			AND serial_numbers.serial_number = '$serial_number'
	";
	
	$verify_sql = "
        SELECT * FROM serial_numbers
        WHERE serial_numbers.manufacturer_id = $new_manu
        	AND serial_numbers.device_id = $device_id
			AND serial_numbers.serial_number = '$serial_number'
    ";
}

if ($new_serial && (empty($new_manu) || $new_manu == NULL && empty($new_device) || $new_device == NULL)) 
{
	//user only wants to update the equipment's serial_number
	if (!($is_clean = check_serial_format($new_serial))) {
		$responseData = create_header("ERROR", "Invalid serial number", "update_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$serial_available = query_serial_number($new_serial);
	if (!$serial_available)
	{
		$responseData = create_header("ERROR", "Duplicate serial entry found", "add_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
		UPDATE serial_numbers SET serial_numbers.serial_number = '$new_serial'
		WHERE serial_numbers.device_id = $device_id
			AND serial_numbers.manufacturer_id = $manufacturer_id
			AND serial_numbers.serial_number = '$serial_number'
	";
	
	$verify_sql = "
		SELECT * FROM serial_numbers
		WHERE serial_numbers.serial_number = '$new_serial'
			AND serial_numbers.device_id = $device_id
			AND serial_numbers.manufacturer_id = $manufacturer_id
	";
}

if ($new_device && $new_manu && (empty($new_serial) || $new_serial == NULL))
{
	//user wants to update the equipment's device and manufacturer
    if (!$valid = validate_int($new_manu) || !$valid = validate_int($new_device)) {
      $responseData = create_header("ERROR", "Invalid manufacturer ID  or device ID format", "update_equipment", "");
      log_activity($dblink, $responseData);
      echo $responseData;
      die();
	}
	
	$device_valid = query_device($new_device);
	if (!$device_valid)
	{
		$responseData = create_header("ERROR", "Invalid device id or inactive", "update_equipment", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	//$device_id is valid if it's successful

	//check if manufacturer_id is valid and active
	$manufacturer_valid = query_manufacturer($new_manu);
	if (!$manufacturer_valid)
	{
		$responseData = create_header("ERROR", "Invalid manufacturer is or inactive", "update_equipment", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	//manufacturer is in db if it's successfull
	
	$sql = "
		UPDATE serial_numbers 
		SET serial_numbers.device_id = $new_device, serial_numbers.manufacturer_id = $new_manu
		WHERE serial_numbers.device_id = $device_id
			AND serial_numbers.manufacturer_id = $manufacturer_id
			AND serial_numbers.serial_number = '$serial_number'
	";
	
	$verify_sql = "
		SELECT * FROM serial_numbers
		WHERE serial_numbers.device_id = $new_device
			AND serial_numbers.manufacturer_id = $new_manu
			AND serial_numbers.serial_number = '$serial_number'
	";
}

if ($new_device && $new_manu && $new_serial)
{
	//user wants to update the equipment's device, manufacturer, and serial
	if (!$valid = validate_int($new_manu) || !$valid = validate_int($new_device)) {
      $responseData = create_header("ERROR", "Invalid manufacturer ID  or device ID format", "update_equipment", "");
      log_activity($dblink, $responseData);
      echo $responseData;
      die();
	}
	
	$device_valid = query_device($new_device);
	if (!$device_valid)
	{
		$responseData = create_header("ERROR", "Invalid device id or inactive", "update_equipment", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	//$device_id is valid if it's successful

	//check if manufacturer_id is valid and active
	$manufacturer_valid = query_manufacturer($new_manu);
	if (!$manufacturer_valid)
	{
		$responseData = create_header("ERROR", "Invalid manufacturer is or inactive", "update_equipment", "");
		echo $responseData;
		log_activity($dblink, $responseData);
		die();
	}
	//manufacturer is in db if it's successfull
	
    if (!($is_clean = check_serial_format($new_serial))) {
		$responseData = create_header("ERROR", "Invalid serial number", "update_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$serial_available = query_serial_number($new_serial);
	if (!$serial_available)
	{
		$responseData = create_header("ERROR", "Duplicate serial entry found", "add_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
	
	$sql = "
		UPDATE serial_numbers
		SET serial_numbers.device_id = $new_device, serial_numbers.manufacturer_id = $new_manu,
			serial_numbers.serial_number = '$new_serial'
		WHERE serial_numbers.device_id = $device_id
			AND serial_numbers.manufacturer_id = $manufacturer_id
			AND serial_numbers.serial_number = '$serial_number'
	";
	
	$verify_sql = "
		SELECT * FROM serial_numbers
		WHERE serial_numbers.device_id = $new_device
			AND serial_numbers.manufacturer_id = $new_manu
			AND serial_numbers.serial_number = '$new_serial'
	";
}

//Run SQL Statements
if (!empty($sql) && !empty($verify_sql))
{
    try {
        $result = $dblink->query($sql);
    } catch (Exception $e) {
        $errorMsg = "Error with sql: " . $e;
        $responseData = create_header("ERROR", $errorMsg, "update_equipment", "");
        log_activity($dblink, $responseData);
        echo $responseData;
        die();
    }

    try {
        $result = $dblink->query($verify_sql);
    } catch (Exception $e) {
        $errorMsg = "Error with sql: " . $e;
        $responseData = create_header("ERROR", $errorMsg, "update_equipment", "");
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
        $responseData = create_header("ERROR", "No device updated", "update_equipment", "");
        log_activity($dblink, $responseData);
        echo $responseData;
        die();
    }
}

$responseData = create_header("ERROR", "Unknown error occured", "update_equipment", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>