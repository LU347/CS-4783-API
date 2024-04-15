<?php
$dblink = db_connect("equipment");
$sql = "SELECT auto_id FROM devices WHERE device_type = '$device_id'";
//$auto_id = -1;

try
{
	$result = $dblink->query($sql);

} catch (Exception $e) {
	$responseData = create_header("ERROR", "Error with sql: $e", "query_device", "");
	echo $responseData;
	die();
}

if ($result->num_rows == 0) {
	$responseData = create_header("Success", "Device type does not exist", "query_device", "");
	echo $responseData;
	$result->close();
	die();
} else {
	$resultArray = $result->fetch_array(MYSQLI_ASSOC);
	$auto_id = $resultArray['auto_id'];
	$responseData = create_header("ERROR", "Device type already exists", "query_device", $auto_id);
	echo $responseData;
	$result->close();
	die();
}
$dblink->close();
?>