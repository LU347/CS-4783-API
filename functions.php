<?php
function db_connect($db)
{
	$username = "webuser";
	$password = "7sjo639RH8SuCpqY";
	$host = "localhost";
	$dblink = new mysqli($host, $username, $password, $db);
	return $dblink;
}

function check_if_clean($row)
{
	if ($row[0] == "" and $row[1] == "" and $row[2] == "")
	{
		return false;
	} 
	elseif ($row[0] == "" or $row[1] == "" or $row[2] == "")
	{
		return false;
		
	}
	elseif (str_contains($row[0], "'") == true or str_contains($row[1], "'") == true or str_contains($row[2], "'") == true)
	{
		return false;
	}
	return true;
}

function get_auto_id($dblink, $key, $value)
{
	switch($key)
	{
		case 'serial_number':
			$sql = "SELECT `auto_id` FROM `serial_numbers` WHERE `serial_number` = '$value'";
			break;
		case 'device_type':
			$sql = "SELECT `auto_id` FROM `devices` WHERE `device_type` = '$value'";
			break;
		case 'manufacturer':
			$sql = "SELECT `auto_id` FROM `manufacturers` WHERE `manufacturer` = '$value'";
			break;
	}
	
	$result = $dblink->query($sql) or 
		die("Error occured with $sql\n");
	
	if ($result->num_rows<=0)
	{
		return false;
	}
	else
	{
		$tmp = $result->fetch_array(MYSQLI_ASSOC);
		return $tmp['auto_id'];
	}
}

function readCSV($file_name)
{
	$file = fopen("/home/ubuntu/parts2/$file_name", "r");
	if (!$file) {
		return false;
	}
	
	//Returns each row as an iterable object
	while (($data = fgetcsv($file)) !== false)
	{
		yield $data;
	}
	fclose($file);
}

function fetch_data($dblink, $sql)
{
	$result = $dblink->query($sql);
	if (!$result)
		echo "<p>Error with sql".$sql."<br>";
	
	if ($result->num_rows<=0)
		return "<p>There are no rows in the given table</p>";
	else
	{
		return $result;
	}
}

function display_data($result)
{
	while ($data = $result->fetch_array(MYSQLI_ASSOC))
	{
		echo '<p>Device Type: '.$data['device_type'].' Manufacturer: '.$data['manufacturer'].' Serial: '.$data['serial_number'];
	}
} 

function calculate_import_speed($start_time, $end_time, $count)
{
	$num_seconds = $end_time - $start_time;
	$num_minutes = $num_seconds / 60;
	$rows_per_second = $count / $num_seconds;
	
	echo "Execution time: ".number_format($num_minutes,2, '.', '')." minutes or ".number_format($num_seconds,2, '.', '')." seconds.\n";
	echo "Input rate: ".number_format($rows_per_second,2, '.', '')." rows per second.\n";
}

function get_current_time($as_float)
{
	return microtime($as_float);
}
?>
