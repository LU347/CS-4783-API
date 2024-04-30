<?php
$dblink = db_connect("equipment");
if (!$dblink) 
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "update_device", "");
	echo $responseData;
	die();
}

if ($device_id === NULL || empty($device_id))
{
	$responseData = create_header("ERROR", "Device id is missing", "update_device", "");
	log_activity($dblink, $reponseData);
	echo $responseData;
	die();
} elseif (!$valid = validate_int($device_id)) {
    $responseData = create_header("ERROR", "Invalid device ID format", "update_device", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

/*
Notes:
Since modify device has the ability to toggle the availability status, I'm assuming
I have to list inactive and active devices?
*/

if ($device_id)
{	
	if ($status && ($updated_str === NULL || empty($updated_str))) {
		//user wants to update status
		$status = strtoupper($status);
		$options = ['ACTIVE', 'INACTIVE'];
		
		if (!in_array($status, $options)) {
			$responseData = create_header("ERROR", "Invalid status", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$sql = "
			UPDATE devices SET status = '$status'
			WHERE auto_id = $device_id
		";
		
		$verify_sql = "
			SELECT * FROM devices 
			WHERE auto_id =$device_id
			AND status = '$status'
		";
	}
	
	if ($updated_str && (!$status || empty($status))) {
		//user wants to update device name
		$updated_str = urldecode($updated_str);
		if (ctype_digit($updated_str) == true) {
			$responseData = create_header("ERROR","Invalid device name format", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$is_available = query_device_duplicate($updated_str);
		if (!$is_available) {
			$responseData = create_header("ERROR", "Duplicate device name found", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$sql = "
			UPDATE devices
			SET device_type = '$updated_str'
			WHERE auto_id = $device_id
		";
				
		$verify_sql = "
			SELECT * FROM devices 
			WHERE device_type = '$updated_str'
				AND auto_id = $device_id
		";
		
	}
	
	if ($updated_str && $status) {
		//user wants to update device and status
		$updated_str = urldecode($updated_str);
		$status = strtoupper($status);
		$options = ['ACTIVE', 'INACTIVE'];
		
		if (!in_array($status, $options)) {
			$responseData = create_header("ERROR", "Invalid status", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		if (ctype_digit($updated_str) == true) {
			$responseData = create_header("ERROR","Invalid device name format", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$sql = "
			UPDATE devices
			SET device_type = '$updated_str', status = '$status'
			WHERE auto_id = $device_id
		";
		
		$verify_sql = "
			SELECT * FROM devices
			WHERE device_type = '$updated_str' 
				AND status = '$status'
				AND auto_id = $device_id
		";
	}
	
	//Run SQL Statements
	if (!empty($sql) && !empty($verify_sql))
	{
		try {
			$result = $dblink->query($sql);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}

		try {
			$result = $dblink->query($verify_sql);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}

		$rows_found = $result->num_rows;
		if ($rows_found > 0) {
			$responseData = create_header("Success", "Device updated", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		} else {
			$responseData = create_header("ERROR", "No device updated", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
	}
}

$responseData = create_header("ERROR", "Unknown error occured", "update_device", "");
log_activity($dblink, $responseData);
echo $responseData;
die();

?>