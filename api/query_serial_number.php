<?php
//need to refactor
$dblink = db_connect("equipment");
$sql = "SELECT `auto_id` FROM `serial_numbers` WHERE `serial_number` = '$serial_number'";

if ($serial_number == NULL)
{
	$responseData = create_header("ERROR", "Serial Number is invalid or missing", "query_serial_number", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($method == NULL)
{
	$responseData = create_header("ERROR", "Must specify method", "query_serial_number", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

//TODO: 
/*
if (strcmp($method == "get_auto_id") == 0)
{
	$sql = "SELECT auto_id FROM serial_numbers WHERE serial_number =" . $serial
}

if (strcmp($method == "check_duplicate") == 0)
{
	
}
*/

try
{
	$result = $dblink->query($sql);
} catch (Exception $e) {
	echo $e;
}

if ($result->num_rows == 0) {
	header( 'Content-Type: application/json' );
	header( 'HTTP/1.1 200 OK' );
	$output[] = 'Status: Success';
	$output[] = 'MSG: Serial does not exist';
	$output[] = 'Action: query_serial_number';
	$responseData = json_encode( $output );
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} else {
	header( 'Content-Type: application/json' );
	header( 'HTTP/1.1 200 OK' );
	$output[] = 'Status: ERROR';
	$output[] = 'MSG: Serial Number already exists';
	$output[] = 'Action: query_serial_number';
	$data = $result->fetch_array(MYSQLI_ASSOC);
	$output[] = 'Data: ' . $data['auto_id'];
	$responseData = json_encode( $output );
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}
$dblink->close();


?>