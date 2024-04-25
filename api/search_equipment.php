<?php
$dblink = db_connect("equipment");
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to database", "search_equipment", "");
	echo $responseData;
	die();
}

$search_methods = ['device', 'manufacturer', 'serial', 'all'];

if ($search_by == NULL)
{
	$responseData = create_header("ERROR", "Invalid search condition", "search_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!($is_clean = check_string_format($search_by))) {
	$responseData = create_header("ERROR", "Invalid search condition format", "search_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} elseif (!in_array($search_by, $search_methods)) {
	$responseData = create_header("ERROR", "Invalid search method", "search_equipment", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

switch($search_by)
{
	case "device":
		include("search_device.php");
		break;
	case "manufacturer":
		include("search_manufacturer.php");
		break;
	case "serial":
		include("search_serial_number.php");
		break;
	case "all":
		include("search_all.php");
		break;
	default:
		$responseData = create_header("ERROR", "Unknown Search Method", "search_equipment", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
		break;
}

$responseData = create_header("ERROR", "Unknown Error occured", "search_equipment", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>