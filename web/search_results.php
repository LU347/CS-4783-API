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
		
						  if (isset($_REQUEST['search_by']))
						  {
							  switch($_REQUEST['search_by'])
						  	  {
								  case "serial":
									  $search_by = $_REQUEST['search_by'];
									  $serial_number = $_REQUEST['serial_number'];
									  $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=serial&serial_number=" . $serial_number;
									  $result = call_api($url);
									  $resultArray = json_decode($result, true);
									  $status = get_msg_status($resultArray);
									  $data = get_data($resultArray);
									  
									  if (strcmp($status, "Success") == 0)
									  {
										  echo "<pre>";
										  echo print_r($data);
										  echo "</pre>";
									  }
									  
									  if (strcmp($status, "ERROR") == 0)
									  {  
										  echo "<h1>No results found</h1>";
									  }
									  
									  break;
								  case "device":
									  $search_by = $_REQUEST['search_by'];
									  $device_id = $_REQUEST['device_id'];
									  $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=device&device_id=" . $device_id;
									  $result = call_api($url);
									  $resultArray = json_decode($result, true);
									  $status = get_msg_status($resultArray);
									  $data = get_data($resultArray);
									  
									  if (strcmp($status, "Success") == 0)
									  {
										  echo "<pre>";
										  echo print_r($data);
										  echo "</pre>";
									  }
									  
									  if (strcmp($status, "ERROR") == 0)
									  {
										  echo "<h1>No results found</h1>";
									  }
									  
									  break;
								  case "manufacturer":
									  $search_by = $_REQUEST['search_by'];
									  $manufacturer_id = $_REQUEST['manufacturer_id'];
									  $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=manufacturer&manufacturer_id=" . $manufacturer_id;
									  $result = call_api($url);
									  $resultArray = json_decode($result, true);
									  $status = get_msg_status($resultArray);
									  $msg = get_msg_data($resultsArray);
									  $data = get_data($resultArray);
									  
									  if (strcmp($status, "Success") == 0)
									  {
										  echo "<pre>";
										  echo print_r($data);
										  echo "</pre>";
									  }
									  
									  if (strcmp($status, "ERROR") == 0)
									  {
										   echo "<h1>No results found</h1>";
									  }
									  
									  break;
								  case "all":
									  $search_by = $_REQUEST['search_by'];
									  $manufacturer_id = $_REQUEST['manufacturer_id'];
									  $device_id = $_REQUEST['device_id'];
									  $serial_number = $_REQUEST['serial_number'];
									  $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=all&manufacturer_id=" . $manufacturer_id . "&device_id=" . $device_id . "&serial_number=" . $serial_number;
									  $result = call_api($url);
									  $resultArray = json_decode($result, true);
									  $status = get_msg_status($resultArray);
									  $data = get_data($resultArray);
									  
									  if (strcmp($status, "Success") == 0)
									  {
										  echo "<pre>";
										  echo print_r($data);
										  echo "</pre>";
									  }
									  
									  if (strcmp($status, "ERROR") == 0)
									  {
										   echo "<h1>No results found</h1>";
									  }
									  
									  break;
								  default:
									  break;
						  	  }
						  }
						  
						 ?>
					</div>
				</div>
            </section>
        </main>
    </body>
</html>