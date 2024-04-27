<?php
$dblink = db_connect("equipment");
if (!$dblink) 
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "update_device", "");
	echo $responseData;
	die();
}

if ($device_id === NULL)
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
	if ($status && $updated_str === NULL) {
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
		
		try {
			$result = $dblink->query($sql);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", "errorMsg", "update_device", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}

		$verify_sql = "
			SELECT * FROM devices 
			WHERE auto_id =$device_id
			AND status = '$status'
		";
					
		try {
			$result = $dblink->query($verify_sql);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", "errorMsg", "update_device", "");
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
	
	if ($updated_str) {
		//user wants to update device
	}
	
	if ($updated_str && $status) {
		//user wants to update device and status
	}
}

$responseData = create_header("ERROR", "Unknown error occured", "update_device", "");
log_activity($dblink, $responseData);
echo $responseData;
$dblink->close();
die();

?>