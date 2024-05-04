<?php
include("../functions.php");
include("api_functions.php");
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
		$device_type = $_REQUEST['device_type'];
		include("add_device.php");
		break;
	case "add_manufacturer":
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$manufacturer = $_REQUEST['manufacturer'];
		include("add_manufacturer.php");
		break;
	case "query_device":
		$device_id = $_REQUEST['device_id'];
		$method = $_REQUEST['method'];
		$device_type = $_REQUEST['device_type'];
		include("query_device.php");
		break;
	case "query_manufacturer":
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$manufacturer = $_REQUEST['manufacturer'];
		$method = $_REQUEST['method'];
		include("query_manufacturer.php");
		break;
	case "query_serial_number":
		$serial_number = $_REQUEST['serial_number'];
		$method = $_REQUEST['method'];
		include("query_serial_number.php");
		break;
	case "list_devices":
		$status = $_REQUEST['status'];
		include("list_devices.php");
		break;
	case "list_manufacturers":
		$status = $_REQUEST['status'];
		include("list_manufacturers.php");
		break;
	case "new_search":
		$status = $_REQUEST['status'];
		$device_id = $_REQUEST['device_id'];
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$serial_number = $_REQUEST['serial_number'];
		include("new_search.php");
		break;
	case "new_update_device":
		$status = $_REQUEST['status'];
		$device_id = $_REQUEST['device_id'];
		$updated_str = $_REQUEST['updated_str'];
		include("new_update_device.php");
		break;
	case "new_update_manufacturer":
		$status = $_REQUEST['status'];
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$updated_str = $_REQUEST['updated_str'];
		include("new_update_manufacturer.php");
		break;
	case "new_update_equipment":
		$device_id = $_REQUEST['device_id'];
		$manufacturer_id = $_REQUEST['manufacturer_id'];
		$serial_number = $_REQUEST['serial_number'];
		$new_device = $_REQUEST['new_device'];
		$new_manu = $_REQUEST['new_manu'];
		$new_serial = $_REQUEST['new_serial'];
		include("new_update_equipment.php");
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