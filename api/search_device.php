<?php
/*
	TODO: Utilize joins, properly check if no rows were found, stop the website
	from hanging if there are no results
*/

if ($device_id == NULL)
{
    $responseData = create_header("ERROR", "Device ID invalid or missing", "search_equipment", "");
    echo $responseData;
    log_activity($dblink, $responseData);
    die();
} elseif (!ctype_digit($device_id)) {
    $responseData = create_header("ERROR", "Device ID is not numeric", "query_device", "");
    log_activity($dblink, $responseData);
    echo $responseData;
    die();
}


//check if device_id is valid and active
$device_valid = query_device($device_id);
if (!$device_valid)
{
	$responseData = create_header("ERROR", "Invalid device id or inactive", "add_equipment", "");
	echo $responseData;
	log_activity($dblink, $responseData);
	die();
}
//$device_id is valid if it's successful

if ($device_valid)
{
	$sql = "SELECT auto_id, manufacturer_id, serial_number FROM serial_numbers WHERE device_id =" . $device_id . " LIMIT 1000";
	
    try {
        $result = $dblink->query($sql);
    } catch (Exception $e) {
        $responseData = create_header("ERROR", "Error with sql $e", "search_equipment", "");
        log_activity($dblink, $responseData);
        echo $responseData;
        die();
    }

    if ($result->num_rows == 0)
    {
        $responseData = create_header("ERROR", "No results found", "search_equipment", "");
        echo $responseData;
        die();
    }

    while ($equipment_data = $result->fetch_array(MYSQLI_ASSOC))
    {
        //need to get manufacturer type per row so each row looks like:
        //hide auto id?
        //auto_id, computer, apple, serialnumber
        $manufacturer = "";
        $manu_sql = "SELECT manufacturer FROM manufacturers WHERE auto_id =".$equipment_data['manufacturer_id'];
        try {
            $manu_query_result = $dblink->query($manu_sql);
            $manu_data = $manu_query_result->fetch_array(MYSQLI_ASSOC);
        } catch (Exception $e) {
            $errorMsg = "Error with sql: " . $e;
            $responseData = create_header("ERROR", $errorMsg, "search_equipment", "");
            log_activity($dblink, $responseData);
            echo $responseData;
            die();
        }
        $row = $device_type . "," . $manu_data['manufacturer'] . "," . $equipment_data['serial_number'];
        $payload[$equipment_data['auto_id']] = $row;
    }	
    $responseData = create_header("Success", "Search by device success", "search_equipment", json_encode($payload));
    echo $responseData;
    die();
}

?>