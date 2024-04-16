<?php
$dblink = db_connect( "equipment" );
$sql = "SELECT `auto_id`, `manufacturer`  FROM `manufacturers` WHERE `status` = 'ACTIVE'";
$result = fetch_data( $dblink, $sql );
$manufacturers = array();
while ( $data = $result->fetch_array( MYSQLI_ASSOC ) ) {
  $manufacturers[ $data[ 'auto_id' ] ] = $data[ 'manufacturer' ];
}
header( 'Content-Type: application/json' );
header( 'HTTP/1.1 200 OK' );
$output[] = 'Status: Success';
$json = json_encode( $manufacturers);
$output[] = 'MSG: ' . $json;
$output[] = 'Action: None';
$responseData = json_encode( $output );
log_activity($dblink, $responseData);
echo $responseData;
die();
?>
