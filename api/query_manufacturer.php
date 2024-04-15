<?php
$dblink = db_connect("equipment");
if (strcmp($method, "get_manufacturer") == 0) 
{
	$sql = "SELECT manufacturer FROM manufacturers WHERE auto_id = $manufacturer_id";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql: $e", "query_manufacturer", "");
		echo $responseData;
		$dblink->close();
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("Error", "Manufacturer does not exist", "query_manufacturer", "");
		echo $responseData;
		$dblink->close();
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$manufacturer = $resultArray['manufacturer'];
		$responseData = create_header("Success", "Manufacturer successfully found", "query_manufacturer", $manufacturer);
		echo $responseData;
		$dblink->close();
		die();
	}
} elseif (strcmp($method, "check_manufacturer_duplicate") == 0) {
	$sql = "SELECT auto_id FROM manufacturers WHERE manufacturer = '$manufacturer_id'";
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR, Error with sql: $e", "query_manufacturer", "");
		echo $responseData;
		die();
	}
	
	if ($result->num_rows == 0)
	{
		$responseData = create_header("Success", "Manufacturer type does not exist", "query_manufacturer", "");
		echo $responseData;
		$dblink->close();
		die();
	} else {
		$resultArray = $result->fetch_array(MYSQLI_ASSOC);
		$auto_id = $resultArray['auto_id'];
		$responseData = create_header("ERROR", "Manufacturer already exists", "query_device", $auto_id);
		echo $responseData;
		$dblink->close();
		die();
	}
}
die();
?>