<?php
$dblink = db_connect( "equipment" );
$sql = "SELECT `auto_id`, `manufacturer`  FROM `manufacturers` WHERE `status` = 'ACTIVE'";
$result = "";
$manufacturers = array();

try {
	$result = $dblink->query($sql);
} catch (Exception $e) {
	$responseData = create_header("ERROR", "Error with sql (fetching manufacturers): $e", "list_manufacturers", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}

if ($result->num_rows == 0)
{
	$responseData = create_header("ERROR", "No manufacturers found", "list_manufacturers", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
} else {
	while ( $data = $result->fetch_array( MYSQLI_ASSOC ) ) {
  		$manufacturers[ $data[ 'auto_id' ] ] = $data[ 'manufacturer' ];
	}
	$jsonManufacturers = json_encode($manufacturers);
	$responseData = create_header("Success", $jsonManufacturers, "list_manufacturers", "");
	log_activity($dblink, $responseData);
	echo $responseData;
	die();
}
$dblink->close();
die();
?>
