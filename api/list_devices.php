<?php
$dblink = db_connect( "equipment" );
$devices_sql = "SELECT `auto_id`, `device_type`  FROM `devices` WHERE `status` = 'ACTIVE'";
$devices_result = fetch_data( $dblink, $devices_sql );
$devices = array();
while ( $devices_data = $devices_result->fetch_array( MYSQLI_ASSOC ) ) {
  $devices[ $devices_data[ 'auto_id' ] ] = $devices_data[ 'device_type' ];
}
header( 'Content-Type: application/json' );
header( 'HTTP/1.1 200 OK' );
$output[] = 'Status: Success';
$jsonDevices = json_encode( $devices );
$output[] = 'MSG: ' . $jsonDevices;
$output[] = 'Action: None';
$responseData = json_encode( $output );
log_activity($dblink, $responseData);
echo $responseData;
die();
?>