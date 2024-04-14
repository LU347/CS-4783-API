<?php
$dblink = db_connect("equipment");
$sql = "SELECT `auto_id` FROM `serial_numbers` WHERE `serial_number` = '$serial_number'";

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
	echo $responseData;
	die();
} else {
	header( 'Content-Type: application/json' );
	header( 'HTTP/1.1 200 OK' );
	$output[] = 'Status: ERROR';
	$output[] = 'MSG: Serial Number already exists';
	$output[] = 'Action: query_serial_number';
	$responseData = json_encode( $output );
	echo $responseData;
	die();
}
$result->close();


?>