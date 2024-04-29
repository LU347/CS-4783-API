<?php
$dblink = db_connect("equipment");
if (!$dblink) 
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "update_manufacturer", "");
	echo $responseData;
	die();
}

if ($manufacturer_id === NULL)
{
	$responseData = create_header("ERROR", "manufacturer id is missing", "update_manufacturer", "");
	log_activity($dblink, $reponseData);
	echo $responseData;
	die();
} elseif (!$valid = validate_int($manufacturer_id)) {
    $responseData = create_header("ERROR", "Invalid manufacturer id format", "update_manufacturer", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}

if ($manufacturer_id)
{	
	if ($status && $updated_str === NULL) {
		//user wants to update status
		$status = strtoupper($status);
		$options = ['ACTIVE', 'INACTIVE'];
		
		if (!in_array($status, $options)) {
			$responseData = create_header("ERROR", "Invalid status", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$sql = "
			UPDATE manufacturers SET status = '$status'
			WHERE auto_id = $manufacturer_id
		";
		
		$verify_sql = "
			SELECT * FROM manufacturers 
			WHERE auto_id =$manufacturer_id
			AND status = '$status'
		";
	}
	
	if ($updated_str && !$status) {
		//user wants to update manufacturer name
		if (ctype_digit($updated_str) == true) {
			$responseData = create_header("ERROR","Invalid manufacturer name format", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		} elseif (!($is_clean = check_string_format($updated_str))) {
			$responseData = create_header("ERROR", "Invalid manufacturer format", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$is_available = query_device_duplicate($updated_str);
		if (!$is_available) {
			$responseData = create_header("ERROR", "Duplicate manufacturer name found", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$sql = "
			UPDATE manufacturers
			SET manufacturer = '$updated_str'
			WHERE auto_id = $manufacturer_id
		";
				
		$verify_sql = "
			SELECT * FROM manufacturers 
			WHERE manufacturer = '$updated_str'
				AND auto_id = $manufacturer_id
		";
		
	}
	
	if ($updated_str && $status) {
		//user wants to update manufacturer and status
		$status = strtoupper($status);
		$options = ['ACTIVE', 'INACTIVE'];
		
		if (!in_array($status, $options)) {
			$responseData = create_header("ERROR", "Invalid status", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		if (ctype_digit($updated_str) == true) {
			$responseData = create_header("ERROR","Invalid manufacturer name format", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		} elseif (!($is_clean = check_string_format($updated_str))) {
			$responseData = create_header("ERROR", "Invalid manufacturer format", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
		
		$sql = "
			UPDATE manufacturers
			SET manufacturer = '$updated_str', status = '$status'
			WHERE auto_id = $manufacturer_id
		";
		
		$verify_sql = "
			SELECT * FROM manufacturers
			WHERE manufacturer = '$updated_str' 
				AND status = '$status'
				AND auto_id = $manufacturer_id
		";
	}
	
	//Run SQL Statements
	if (!empty($sql) && !empty($verify_sql))
	{
		try {
			$result = $dblink->query($sql);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}

		try {
			$result = $dblink->query($verify_sql);
		} catch (Exception $e) {
			$errorMsg = "Error with sql: " . $e;
			$responseData = create_header("ERROR", $errorMsg, "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}

		$rows_found = $result->num_rows;
		if ($rows_found > 0) {
			$responseData = create_header("Success", "manufacturer updated", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		} else {
			$responseData = create_header("ERROR", "No manufacturer updated", "update_manufacturer", "");
			log_activity($dblink, $responseData);
			echo $responseData;
			die();
		}
	}
}

$responseData = create_header("ERROR", "Unknown error occured", "update_manufacturer", "");
log_activity($dblink, $responseData);
echo $responseData;
$dblink->close();
die();

?>