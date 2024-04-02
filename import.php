<?php

include("functions.php");
//$test_dblink=db_connect("test");
//$error_dblink = db_connect("error_logs");
$equipment_dblink = db_connect("equipment");
$csv = readCSV($argv[1]);
$start_time = get_current_time(true);

echo "Processing: $argv[1] Start time: $start_time\n";

$values = "";
$default_sql = "INSERT INTO `serial_numbers` (`device_id`, `manufacturer_id`, `serial_number`) VALUES";
$count = 1;

foreach ($csv as $row) 
{	
	//skips rows with errors and duplicates
	if (check_if_clean($row) == false)
	{
		$count++;
		continue;
	} elseif (get_auto_id($equipment_dblink, 'serial_number', $row[2]) !== false)
	{
		$count++;
		continue;
	}
	
	//checks if device type or manufacturer is already in db
    $device_id = get_auto_id($equipment_dblink, 'device_type', $row[0]);
    $manu_id = get_auto_id($equipment_dblink, 'manufacturer', $row[1]);
    $serial_num = false; //get_auto_id($equipment_dblink, 'serial_number', $row[2]);

    //Inserts device_id and manufacturer_id if it is not taken
    if ($device_id === false)
    {
        $sql = "INSERT INTO `devices` (`device_type`, `status`) VALUES ('$row[0]', 'ACTIVE')";
        $result = $equipment_dblink->query($sql) or
            die ("Error occured with $sql\n");
        $device_id = $equipment_dblink->insert_id;
    } 

    if ($manu_id === false)
    {
        $sql = "INSERT INTO `manufacturers` (`manufacturer`, `status`) VALUES ('$row[1]', 'ACTIVE')";
        $result = $equipment_dblink->query($sql) or
            die ("Error occured with $sql\n");
        $manu_id = $equipment_dblink->insert_id;
    }

    if ($serial_num === false)
    {		
        $sql = "INSERT INTO `serial_numbers` (`device_id`, `manufacturer_id`, `serial_number`) VALUES";
        $values .= "(" . "'" . $device_id . "'," . "'" . $manu_id . "'," . "'" . $row[2] . "'),";
    }
	
	$sql = "INSERT INTO `serial_numbers` (`device_id`, `manufacturer_id`, `serial_number`) 
		    VALUES ($device_id, $manu_id, '$row[2]')";
	try {
		$result = $equipment_dblink->query($sql);
	} catch (Exception $e)
	{
		echo $e . "\n";
	}
	/*
    if ($count === 25000) {	//should equal 25000
        $values = rtrim($values, ",");
        $sql .= $values;
        try {
            $result = $equipment_dblink->query($sql);
        } 	catch (Exception $e) {
                echo $e;
        }
        $values = "";
        $sql = $default_sql;
		$count = 0;
        echo $count . "\n";
        echo "Part done\n";
    }
	*/
    $count++;	
}
$end_time = get_current_time(true);

//$test_dblink->close();
$equipment_dblink->close();

echo "P_ID: $argv[1] End time: $end_time\n";

calculate_import_speed($start_time, $end_time, $count);
?>