<?php
/*
	if device_id == null
		person is searching by manufacturer or serial number or just manufacturer
	if manufacturer_id == null
		person is searching by device or serial number
*/

if ($search_by == NULL)
{
	/*
		search by device = device
		search by manufacturer = manufacturer
		search by serial = serial
		search all (device, manufacturer, serial) = all
	*/
	$responseData = create_header("ERROR", "Invalid Search Condition", "search_equipment", "");
	echo $responseData;
	die();
}

//Check if device or manufacturer is valid
if ($search_by == "device")
{
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?device_id=" . $device_id;
    $result = call_api($url);
    $resultsArray = json_decode($result, true); //turns result into array
    $status = trim(get_msg_status($resultsArray));
    $msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)
	$data = trim(substr($resultsArray[3], 5));
    
	if (strcmp($status, "ERROR") == 0) 
	{
		//means device type is in database
		$dblink = db_connect("equipment");
		$sql = "SELECT auto_id, device_id, manufacturer_id, serial_number FROM serial_numbers WHERE device_id = '$data' LIMIT 10"; 
		echo "<h1>$sql</h1>";
	}
}
die();
?>