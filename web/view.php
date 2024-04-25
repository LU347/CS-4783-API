<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="../index.css">
    </head>
    <body>
        <nav>
            <ul class="navbar">
                <li><a href="">Home</a></li>
                <li><a href="search.php">Search Equipment</a></li>
                <li><a href="add.php">Add Equipment</a></li>
				<li><a href="update.php">Update Equipment</a></li>
                <li><a href="">View Equipment</a></li>
            </ul>
        </nav>
        <main>
            <section class="view-page">
				<?php
					ob_start();
                    include("../api/api_functions.php");
                    $result = call_api("https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_devices");
                    $resultsArray = json_decode($result, true);
                    $devices = get_msg_data($resultsArray);

                    $result = call_api("https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_manufacturers");
                    $resultsArray = json_decode($result, true);
                    $manufacturers = get_msg_data($resultsArray);
				?>
				<div class="parent"><h1>View Equipment</h1></div>
				<div class="parent" id="singleEquipment">
					<form method="POST" class="search-bar" action="">
						<label for="device">Device:</label>
						<select name="device_id">
							<option selected disabled>Choose Device</option>
							<?php
                              foreach($devices as $key=>$value)
                              {
                                  echo '<option value="'.$key.'">'.$value.'</option>';
                              }
                            ?>
						</select>
						<label for="manufacturer">Manufacturer:</label>
						<select name="manufacturer_id">
							<option selected disabled>Choose Manufacturer</option>
							<?php
                              foreach($manufacturers as $key=>$value)
                              {
                                  echo '<option value="'.$key.'">'.$value.'</option>';
                              }
                            ?>
						</select>
						<label for="serial">Serial #:</label>
						<input type="text" id="serialInput" name="serial_number" placeholder="Format: SN-09091asda309asd">
						<button type="submit" value="submit" name="submit-equipment">View Equipment</button>
					</form>
				</div>
				<div class="parent" id="allEquipment">
					<form method="POST" class="search-bar" action="">
						<button type="submit" value="submit-all" name="submit-all">View All Equipment</button>
					</form>
				</div>
            </section>
			<section class="results">
				<div class="parent">
					<?php
						ob_start();
						if (isset($_POST['submit-equipment'])) 
						{
							  $manufacturer_id = $_REQUEST['manufacturer_id'];
							  $device_id = $_REQUEST['device_id'];
							  $serial_number = $_REQUEST['serial_number'];
							  $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=all&manufacturer_id=" . $manufacturer_id . "&device_id=" . $device_id . "&serial_number=" . $serial_number;
							$result = call_api($url);
							  display_results($result);
						}
					
						if (isset($_POST['submit-all']))
						{
							$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_all_equipment";
							$result = call_api($url);
							display_results($result);
						}
					?>
				</div>
			</section>
        </main>
    </body>
	<script>
		
    </script>
</html>