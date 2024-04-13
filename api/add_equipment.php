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
    $output[]='MSG: Invalid or missing serial number ID';
    $output[]='Action: None';
    $responseData=json_encode($output);
    echo $responseData;
	die();
}

$ch = curl_init("https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/query_serial_number");
$data="test";
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//ignore ssl
curl_setopt($ch, CURLOPT_POST,1);//tell curl we are using post
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//this is the data
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//prepare a response
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'content-type: application/x-www-form-urlencoded',
    'content-length: '.strlen($data))
            );
$result=curl_exec($ch);
curl_close($ch);
$data=$json_decode($result);
?>