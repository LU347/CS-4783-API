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
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]='Status: ERROR';
    $output[]='MSG: Invalid or missing device ID';
    $output[]='Action: query_device';
    $responseData=json_encode($output);
    echo $responseData;
	die();
} 

if ($manufacturer_id == NULL)
{
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]='Status: ERROR';
    $output[]='MSG: Invalid or missing manufacturer ID';
    $output[]='Action: query_manufacturer';
    $responseData=json_encode($output);
    echo $responseData;
	die();
}

if ($serial_number == NULL)
{
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]='Status: ERROR';
    $output[]='MSG: Missing serial number ID';
    $output[]='Action: None';
    $responseData=json_encode($output);
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
	header('Content-Type: application/json');
    header('HTTP/1.1 200 OK');
    $output[]='Status: ERROR';
    $output[]='MSG: ' . $msg;
    $output[]='Action: query_serial_number';
    $responseData=json_encode($output);
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
	} catch(Exception $e)
	{
		header('Content-Type: application/json');
    	header('HTTP/1.1 200 OK');
    	$output[]='Status: ERROR';
    	$output[]='MSG: Error with SQL ' . $e;
    	$output[]='Action: add_equipment';
    	$responseData=json_encode($output);
    	echo $responseData;
		die();
	}
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$output[]='MSG: Equipment successfully added!';
	$output[]='Action: add_equipment';
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
//insert
/*
if (strcmp($status, "Success") == 0)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[]='Status: Success';
	$output[]='MSG: Equipment successfully added!';
	$output[]='Action: add_equipment';
	$output[] = $serial_query_result;
	$responseData=json_encode($output);
	echo $responseData;
	die();
}
*/

/*
//success message
header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output[]='Status: Success';
$output[]='MSG: ' . 'deviceid=' . $device_id . 'manu_id:' . $manufacturer_id . 'serial:' . $serial_number;
$output[]='Action: None';
$output[] = $serial_query_result;
$responseData=json_encode($output);
echo $responseData;
die();
*/
?>