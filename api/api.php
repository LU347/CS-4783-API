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
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		include("add_manufacturer.php");
		break;
	case "query_device":
		$device_id = $_REQUEST['device_id'];
		$method = $_REQUEST['method'];
		include("query_device.php");
		break;
	case "query_manufacturer":
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$method = $_REQUEST['method'];
		include("query_manufacturer.php");
		break;
	case "query_serial_number":
		$serial_number = $_REQUEST['serial_number'];
		$method = $_REQUEST['method'];
		include("query_serial_number.php");
		break;
	case "list_devices":
		include("list_devices.php");
		break;
	case "list_manufacturers":
		include("list_manufacturers.php");
		break;
	case "search_equipment":
		$search_by = $_REQUEST['search_by'];
		$device_id = $_REQUEST['device_id'];
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$serial_number = $_REQUEST['serial_number'];
		include("search_equipment.php");
		break;
	case "update_device":						//error checking done
		$device_id = $_REQUEST['device_id'];
		$updated_str = $_REQUEST['updated_str'];
		include("update_device.php");
		break;
	case "update_manufacturer":
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$updated_str = $_REQUEST['updated_str'];
		include("update_manufacturer.php");
		break;
	case "update_serial_number":
		$serial_number = $_REQUEST['serial_number'];
		$updated_str = $_REQUEST['updated_str'];
		include("update_serial_number.php");
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