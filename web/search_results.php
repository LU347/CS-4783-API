<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="../index.css">
    </head>
    <body>
        <nav>
            <ul class="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="search.php">Search Equipment</a></li>
                <li><a href="add.php">Add Equipment</a></li>
				<li><a href="update.php">Update Equipment</a></li>
            </ul>
        </nav>
        <main>
            <section class="search-results-page">
                <div class="parent">
                    <h1>Search Results</h1>
                </div>
				<div class="search-results-container">
					<div class="container">
						<?php
						  ob_start();
						  include("../api/api_functions.php");
						  if (isset($_REQUEST['search_by']) && isset($_REQUEST['device_id']))
						  {
							  $search_by = $_REQUEST['search_by'];
							  $device_id = $_REQUEST['device_id'];
                              $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=" . $search_by . "&device_id=" . $device_id;
                              $result = call_api($url);
                              $resultArray = json_decode($result, true);
                              $status = get_msg_status($resultsArray);
                              $data = get_data($resultArray);
                              echo "<pre>";
							  echo print_r($data);
							  echo "</pre>";
						  }
						
                          if (isset($_REQUEST['search_by']) && isset($_REQUEST['manufacturer_id']))
                          {
                              $search_by = $_REQUEST['search_by'];
                              $manufacturer_id = $_REQUEST['manufacturer_id'];
                              $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=" . $search_by . "&manufacturer_id=" . $manufacturer_id;
                              $result = call_api($url);
                              $resultArray = json_decode($result, true);
                              $status = get_msg_status($resultsArray);
                              $data = get_data($resultArray);
                              echo "<pre>";
                              echo print_r($data);
                              echo "</pre>";
                          }
						
                          if (isset($_REQUEST['search_by']) && isset($_REQUEST['serial_number']))
                          {
                              $search_by = $_REQUEST['search_by'];
                              $serial_number = $_REQUEST['serial_number'];
                              $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=" . $search_by . "&serial_number=" . $serial_number;
                              $result = call_api($url);
                              $resultArray = json_decode($result, true);
                              $status = get_msg_status($resultsArray);
                              $data = get_data($resultArray);
                              echo "<pre>";
                              echo print_r($data);
                              echo "</pre>";
                          }
						
                          if (isset($_REQUEST['search_by']) && isset($_REQUEST['serial_number']) && isset($_REQUEST['device_id']) && isset($_REQUEST['manufacturer_id']))
                          {
                              $search_by = $_REQUEST['search_by'];
                              $serial_number = $_REQUEST['serial_number'];
							  $device_id = $_REQUEST['device_id'];
							  $manufacturer_id = $_REQUEST['manufacturer_id'];
                              $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=" . $search_by . "&serial_number=" . $serial_number . "&device_id=" . $device_id . "&manufacturer_id=" . $manufacturer_id;
                              $result = call_api($url);
                              $resultArray = json_decode($result, true);
                              $status = get_msg_status($resultsArray);
                              $data = get_data($resultArray);
                              echo "<pre>";
                              echo print_r($data);
                              echo "</pre>";
                          }
						 ?>
					</div>
				</div>
            </section>
        </main>
    </body>
</html>