<?php
$dblink = db_connect( "equipment" );
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to db", "list_devices", "");
	echo $responseData;
	die();
}

if ($status == NULL) {
	//show active devices by default
	$sql = "SELECT `auto_id`, `device_type` FROM `devices` WHERE `status` = 'ACTIVE'";
} elseif (strcmp($status, "both") === 0) {
	$sql = "SELECT `auto_id`, `device_type` FROM `devices` 
			WHERE `status` = 'ACTIVE'
				OR `status` = 'INACTIVE'";
}

if (!empty($sql))
{
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
		$devices = array();
		while ( $data = $result->fetch_array( MYSQLI_ASSOC ) ) {
			$devices[ $data[ 'auto_id' ] ] = $data[ 'device_type' ];
		}
		$jsonDevices = json_encode($devices);
		$responseData = create_header("Success", $jsonDevices, "list_devices", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$responseData = create_header("ERROR", "No results found", "list_devices", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}


$responseData = create_header("ERROR", "Unknown Error occured", "list_devices", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>