<?php
include("../functions.php");
include("api_functions.php");
/*$url=$_SERVER['REQUEST_URI'];
header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$output[]='Status: ERROR';
$output[]='MSG: System Disabled';
$output[]='Action: None';
//log_error($_SERVER['REMOTE_ADDR'],"SYSTEM DISABLED","SYSTEM DISABLED: $endPoint",$url,"api.php");*/
$url=$_SERVER['REQUEST_URI'];
$path = parse_url($url, PHP_URL_PATH);
$pathComponents = explode("/", trim($path, "/"));
$endPoint=$pathComponents[1];

switch($endPoint)
{
    case "add_equipment":
		$device_id = $_REQUEST['device_id'];
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$serial_number = $_REQUEST['serial_number'];
        include("add_equipment.php");
        break;
	case "add_device":
		$device_id = $_REQUEST['device_id'];
		include("add_device.php");
		break;
	case "add_manufacturer":
		$new_manufacturer = $_REQUEST['new_manufacturer'];
		break;
	case "query_device":
		$device_id = $_REQUEST['device_id'];
		include("query_device.php");
		break;
	case "query_manufacturer":
		break;
	case "query_serial_number":
		$serial_number = $_REQUEST['serial_number'];
		include("query_serial_number.php");
		break;
	case "list_devices":
		include("list_devices.php");
		break;
	case "list_manufacturers":
		include("list_manufacturers.php");
		break;
    default:
        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        $output[]='Status: ERROR';
        $output[]='MSG: Invalid or missing endpoint';
        $output[]='Action: None';
        $responseData=json_encode($output);
        echo $responseData;
        break;
}
die();
?>