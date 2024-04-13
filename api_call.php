<?php
$ch = curl_init( "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_devices" );
$data = "";
curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); //ignore ssl
curl_setopt( $ch, CURLOPT_POST, 1 ); //tell curl we are using post
curl_setopt( $ch, CURLOPT_POSTFIELDS, $data ); //this is the data
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); //prepare a response
curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
  'content-type: application/x-www-form-urlencoded',
  'content-length: ' . strlen( $data ) ) );
$result = curl_exec( $ch );
curl_close( $ch );
$nonJsonResult = json_decode($result, true);
echo "<pre>";
print_r($nonJsonResult);
echo "</pre>";
die();
?>