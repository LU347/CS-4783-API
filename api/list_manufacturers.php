<?php
$dblink = db_connect( "equipment" );
if (!$dblink)
{
	$responseData = create_header("ERROR", "ERROR connecting to db", "list_manufacturers", "");
	echo $responseData;
	die();
}

if ($status == NULL) {
	//show active devices by default
	$sql = "SELECT `auto_id`, `manufacturer` FROM `manufacturers` WHERE `status` = 'ACTIVE'";
} elseif (strcmp($status, "both") === 0) {
	$sql = "SELECT `auto_id`, `manufacturer` FROM `manufacturers` 
			WHERE `status` = 'ACTIVE'
				OR `status` = 'INACTIVE'";
}


if (!empty($sql)) 
{
	try {
		$result = $dblink->query($sql);
	} catch (Exception $e) {
		$responseData = create_header("ERROR", "Error with sql (fetching manufacturers): $e", "list_manufacturers", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}

	$rows_found = $result->num_rows;
	if ($rows_found > 0)
	{
	   $manufacturers = array();
	   while ( $data = $result->fetch_array( MYSQLI_ASSOC ) ) {
			$manufacturers[ $data[ 'auto_id' ] ] = $data[ 'manufacturer' ];
		}
		$jsonManufacturers = json_encode($manufacturers);
		$responseData = create_header("Success", $jsonManufacturers, "list_manufacturers", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	} else {
		$responseData = create_header("ERROR", "No results found", "list_manufacturers", "");
		log_activity($dblink, $responseData);
		echo $responseData;
		die();
	}
}

$responseData = create_header("ERROR", "Invalid Status / Unknown Error occurred", "list_manufacturers", "");
log_activity($dblink, $responseData);
echo $responseData;
die();
?>
