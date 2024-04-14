<?php
$dblink = db_connect("equipment");
$sql = "SELECT auto_id FROM manufacturers WHERE manufacturer = '$manufacturer_id'";

try
{
	$result = $dblink->query($sql);
} catch (Exception $e) {
	$responseData = create_header("ERROR", "Error with sql: $e", "query_manufacturer");
	echo $responseData;
	die();
}

if ($result->num_rows == 0) {
	$responseData = create_header("Success", "Manufacturer does not exist", "query_manufacturer");
	echo $responseData;
	$result->close();
	die();
} else {
	$responseData = create_header("ERROR", "Manufacturer already exists", "query_manufacturer");
	echo $responseData;
	$result->close();
	die();
}
?>