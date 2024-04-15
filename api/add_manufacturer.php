<?php
if ($manufacturer_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing manufacturer", "add_manufacturer", "");
	echo $responseData;
	die();
}

$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?manufacturer_id=" . $manufacturer_id . "&method=check_manufacturer_duplicate";

$result = call_api($url);
$resultsArray = json_decode($result, true);
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1],4));

if (strcmp($status, "ERROR") == 0)
{
	$responseData = create_header("ERROR", $msg, "query_manufacturer", "");
	echo $responseData;
	die();
}

$dblink = db_connect("equipment");

if (strcmp($status, "Success") == 0)
{
	$sql = "INSERT INTO  manufacturers (manufacturer, status)
			VALUES ('$manufacturer_id', 'ACTIVE')";
	
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$errorMsg = "Error with SQL: " . $e;
		$responseData = create_header("ERROR", $errorMsg, "add_manufacturer", "");
		echo $responseData;
		die();
	}
	$responseData = create_header("Success", "Manufacturer successfully added!", "add_manufacturer", "");
	echo $responseData;
	die();
}
$dblink->close();

die();
?>