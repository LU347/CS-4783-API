<?php
//this is only for functions that will be used repetitively, such as sending messages
//and logging errors
function call_api($url) 
{
  $ch = curl_init($url);
  $data = "";
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //ignore ssl
  curl_setopt($ch, CURLOPT_POST, 1 ); //tell curl we are using post
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data ); //this is the data
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //prepare a response
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'content-type: application/x-www-form-urlencoded',
    'content-length: ' . strlen($data) ) );
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

function create_header($status, $msg, $action, $data)
{
	header('Content-Type: application/json');
	header('HTTP/1.1 200 OK');
	$output[] = 'Status: ' . $status;
	$output[] = 'MSG: ' . $msg;
	$output[] = 'Action: ' . $action;
	$output[] = 'Data: ' . $data; //optional?
	$responseData = json_encode( $output );
	return $responseData;
}

function get_msg_status($msg)
{
	$tmp = $msg[0];	//placeholder for the status row
    $status = explode("Status:", $tmp); //explodes status to get the status variable
	$status[1] = trim($status[1]); //this should get success or error
	return $status[1];
}

function get_msg_data($msg) 
{
  $tmp = $msg[1];
  $payload_Data = explode( "MSG:", $tmp);
  return json_decode( $payload_Data[1], true);
}

function get_data($msg)
{
  $tmp = $msg[3];
  $payload_Data = explode("Data:", $tmp);
  return json_decode( $payload_Data[1], true);
}

function log_activity($dblink, $responseData)
{
    //not including Data: 
    $resultsArray = json_decode($responseData, true);
    $log[] = $resultsArray[0];
    $log[] = $resultsArray[1];
    $log[] = $resultsArray[2];
    $jsonLog = json_encode($log, true);
    $sql = "INSERT INTO api_logs (log) VALUES('" . $jsonLog . "')";

    try {
        $result = $dblink->query($sql);	
    } catch (Exception $e)
    {
        $responseData = create_header("ERROR", "error uploading log", "log_activity", "");
        echo $responseData;
        die();
    }
	
	$dblink->close();
}

function check_serial_format($serial_number)
{
	if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $serial_number)) 
	{
		return false;
	} 
	/*
	elseif (!preg_match('/SN-[\d\w]+/', $serial_number)) {
		return false;
	}
	*/
	return true;
}

function format_serial($serial)
{
	$split_serial = explode("-", $serial);
	return $split_serial[0] . "-" . strtolower($split_serial[1]);
}

function check_if_digit($id) 
{
	if (!ctype_digit($id))
		return false;
	return true;
}

function query_device($device_id)
{
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_device?method=check_status&device_id=" . $device_id;
	$results = call_api($url);
	$resultsArray = json_decode($results, true);
	$status = trim(get_msg_status($resultsArray));
	$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

	if (strcmp($status, "ERROR") == 0)
	{
		return false;
	}
	
	if (strcmp($status, "Success") == 0)
	{
		return true;
	}
	
	return false;
}

function query_manufacturer($manufacturer_id)
{
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_manufacturer?method=check_status&manufacturer_id=" . $manufacturer_id;
	$results = call_api($url);
	$resultsArray = json_decode($results, true);
	$status = trim(get_msg_status($resultsArray));
	$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)

	if (strcmp($status, "Success") == 0)
	{
		return true;
	}
	
	if (strcmp($status, "ERROR") == 0)
	{
		return false;
	}
	
	return false;
}

function query_serial_number($serial_number)
{
	$serial_number = trim($serial_number);
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_serial_number?method=check_duplicates&serial_number=" . $serial_number;
	$results = call_api($url);
	$resultsArray = json_decode($results, true);
	$status = trim(get_msg_status($resultsArray));
	$msg = trim(substr($resultsArray[1], 4)); //this should get the msg: line (if it's not json)
	
	if (strcmp($status, "Success") == 0)
	{
		return true;
	}
	
	if (strcmp($status, "ERROR") == 0)
	{
		return false;
	}
	
	return false;
}

function check_string_format($string)
{
	if (ctype_digit($string))
	{
		return false;
	}
	
	if (!preg_match('/^([a-zA-Z]+\s)*[a-zA-Z]+$/', $string))
	{
		return false;
	}
	return true;
}

function display_results($result)
{
    $resultArray = json_decode($result, true);
    $status = get_msg_status($resultArray);

    if (strcmp($status, "Success") == 0) {
        $data = get_data($resultArray);
		$num_results = count($data);
		
		echo "<table class='view-table' style='overflow-x:auto;'>
            <tr>
              <th>DEVICE TYPE</th>
              <th>MANUFACTURER</th>
              <th>SERIAL NUMBER</th>
            </tr>";

		for ($i = 0; $i < $num_results; $i++) {
			$row = explode(",", $data[$i]);
			echo "<tr>";
			for ($j = 0; $j < 3; $j++) {
				echo "<td>";
				echo $row[$j];
				echo "</td>";
			}
			echo "</tr>";
		}
		
		echo "</table>";
    }

    if (strcmp($status, "ERROR") == 0) {
        $msg = explode("MSG:", $resultArray[1]);
		echo "<div class=parent>";
        echo "<h2>$msg[1]</h2>";
		echo "</div>";

    }
}
?>