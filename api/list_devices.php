<?php
$dblink = db_connect( "equipment" );
$devices_sql = "SELECT `auto_id`, `device_type`  FROM `devices` WHERE `status` = 'ACTIVE'";
$result = "";
$devices = array();

try {
	$result = $dblink->query($devices_sql);
} catch (Exception $e) {
	$responseData = create_header("ERROR", "Error with sql (fetching devices): $e", "list_devices", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($result->num_rows == 0)
{
	$responseData = create_header("ERROR", "No devices found", "list_devices", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} else {
	while ( $data = $result->fetch_array( MYSQLI_ASSOC ) ) {
  		$devices[ $data[ 'auto_id' ] ] = $data[ 'device_type' ];
	}
	$jsonDevices = json_encode($devices);
	$responseData = create_header("Success", $jsonDevices, "list_devices", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}
?>