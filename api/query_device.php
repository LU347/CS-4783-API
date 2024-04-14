<?php
$dblink = db_connect("equipment");
$sql = "SELECT auto_id FROM devices WHERE device_type = '$device_id'";

try
{
	$result = $dblink->query($sql);
} catch (Exception $e) {
	$responseData = create_header("ERROR", "Error with sql: $e", "query_device");
	echo $e;
	die();
}

if ($result->num_rows == 0) {
	$responseData = create_header("Success", "Device type does not exist", "query_device");
	echo $responseData;
	$result->close();
	die();
} else {
	$responseData = create_header("ERROR", "Device type already exists", "query_device");
	echo $responseData;
	$result->close();
	die();
}
?>