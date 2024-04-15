<?php
/*
function handleError()
{
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]='Status: ERROR';
	
	if ($device_id == NULL) 
	{
		$output[] = 'MSG: Invalid or missing device ID';
		$output[] = 'Action: query_device';
	}
	if ($manufacturer_id == NULL)
	{
		    $output[]='MSG: Invalid or missing manufacturer ID';
    		$output[]='Action: query_manufacturer';
	}
	$responseData = json_encode($output);
	echo $responseData;
	die();
}
*/
if ($device_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "query_device", "");
    echo $responseData;
	die();
} 

if ($manufacturer_id == NULL)
{
	$responseData = create_header("ERROR", "Invalid or missing device ID", "query_manufacturer", "");
    echo $responseData;
	die();
}

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Missing serial number ID", "None", "");
    echo $responseData;
	die();
}

$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_serial_number?serial_number=" . $serial_number;
$serial_query_result = call_api($url);

$resultsArray = json_decode($serial_query_result, true); //turns result into array
$status = trim(get_msg_status($resultsArray));
$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

if (strcmp($status, "ERROR") == 0)
{
	$responseData = create_header("ERROR", $msg, "query_serial_number", "");
    echo $responseData;
	die();
} 

$dblink = db_connect("equipment");

if (strcmp($status, "Success") == 0)
{
	$sql = "INSERT INTO serial_numbers (device_id, manufacturer_id, serial_number) 
	VALUES ('$device_id', '$manufacturer_id', '$serial_number')";
	
	try {
		$result = $dblink->query($sql);
	} catch(Exception $e) {
		$errorMsg = "Error with SQL" . $e;
		$responseData = create_header("ERROR", $errorMsg, "add_equipment", "");
    	echo $responseData;
		die();
	}
	$responseData = create_header("Success", "Equipment successfully added!", "add_equipment", "");
	echo $responseData;
	die();
}
$dblink->close();
?>