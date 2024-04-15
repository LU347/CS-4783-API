<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="../index.css">
    </head>
    <body>
        <nav>
            <ul class="navbar">
                <li><a href="index.php">Home</a></li>
                <li><a href="">Search Equipment</a></li>
                <li><a href="add.php">Add Equipment</a></li>
            </ul>
        </nav>
        <main>
			<div class="parent">
                    <h1>Search Equipment</h1>
            </div>
            <section class="search-page" id="searchPage">
                
				<div class="search-parent">
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
					<div class="search-grid">
						<div class="form-container">
							<form method="POST" action="">
								<label for="device_id">Device Type:</label><br>
								<select name="device_id">
									<option selected disabled>Choose Here</option>
									<?php
										foreach($devices as $key=>$value)
										{
											echo '<option value="'.$key.'">'.$value.'</option>';
										}
									?>
								</select>
								<label for="manufacturer_id">Manufacturer:</label><br>
								<select name="manufacturer_id">
									<option selected disabled>Choose Here</option>
									<?php
										foreach($manufacturers as $key=>$value)
										{
											echo '<option value="'.$key.'">'.$value.'</option>';
										}
									?>
								</select>
								<label for="serial_number">Serial Number:</label><br>
								<input type="text" name="serial_number" placeholder="Format: SN-xxxxx..">
								<button type="submit" value="submit-search" name="submit-search">Search</button>
							</form>
						</div>
						<div class="form-container">
							<form method="POST" action="">
								<label for="device_id">Search by Device Type:</label>
								<select name="device_id">
									<option selected disabled>Choose Here</option>
									<?php
										foreach($devices as $key=>$value)
										{
											echo '<option value="'.$key.'">'.$value.'</option>';
										}
									?>
								</select>
								<button type="submit" value="submit-search-device" name="submit-search-device">Search Device</button>
							</form>
							<br>
							<form method="POST" action="">
								<label for="manufacturer_id">Search by Manufacturer:</label>
								<select name="manufacturer_id">
									<option selected disabled>Choose Here</option>
									<?php
										foreach($manufacturers as $key=>$value)
										{
											echo '<option value="'.$key.'">'.$value.'</option>';
										}
									?>
								</select>
								<button type="submit" value="submit-search-manufacturer" name="submit-search-manufacturer">Search Manufacturer</button>
							</form>
							<br>
							<form method="POST" action="">
								<label for="serial_number">Search by Serial Number:</label>
								<input type="text">
								<button type="submit" value="submit-search-serial" name="submit-search-serial">Search Serial Number</button>
							</form>
						</div>
					</div>
				</div>
            </section>
        </main>
    </body>
</html>
<?php
ob_start();
if (isset($_POST['submit-search-device']))
{
    $search_by = "device";
    $device_id = $_POST['device_id'];
	/*
    $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=" . $search_by . "&device_id=" . $device_id;
    $result = call_api($url);
    $resultArray = json_decode($result, true);
    $status = get_msg_status($resultsArray);
    $data = get_data($resultArray);
	$jsonData = json_encode($data);
	*/
	header("Location: search_results.php?search_by=$search_by&device_id=$device_id");
	die();
}
?>
<?php
ob_start();
if (isset($_POST['submit-search-manufacturer']))
{
    $search_by = "manufacturer";
    $manufacturer_id = $_POST['manufacturer_id'];
	/*
    $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/search_equipment?search_by=" . $search_by . "&device_id=" . $device_id;
    $result = call_api($url);
    $resultArray = json_decode($result, true);
    $status = get_msg_status($resultsArray);
    $data = get_data($resultArray);
	$jsonData = json_encode($data);
	*/
	header("Location: search_results.php?search_by=$search_by&manufacturer_id=$manufacturer_id");
	die();
}
?>
