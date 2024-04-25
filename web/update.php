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
				<li><a href="">Update Equipment</a></li>
            </ul>
        </nav>
        <main>
			<section class="update-home-page">
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
				<div class="parent">
                    <div class="update-home-grid">
						<div class="card">
                            <h3>Update Equipment</h3>
                            <p><em>Update Existing Equipment</em></p>
                            <button name="update-equipment" onclick="location.href='../web/update_equipment.php'">Click to Update Equipment</button>
                        </div>
                        <div class="card">
                            <h3>Update Device</h3>
                            <p><em>Update an existing device type</em></p>
                            <button name="update-device" onclick="toggleNewForms()">Click to Update Device</button>
                        </div>
                        <div class="card">
                            <h3>Update Manufacturer</h3>
                            <p><em>Update an existing manufacturer</em></p>
                            <button name="update-manufacturer" onclick="toggleNewForms()">Click to Update Manufacturer</button>
                        </div>
						<div class="card">
                            <h3>Update Serial Number</h3>
                            <p><em>Update an existing Serial number with a valid device id, and manufacturer id</em></p>
                            <button name="update-serial" onclick="toggleNewForms()">Click to Update Serial Number</button>
                        </div>
                    </div>
                </div>
			</section>
			<section class="new-device-manu" id="deviceForms" style="display: none">
				<div class="new-form-container">
					<form method="POST" action="">
						<label for="devices">Select Device:</label>
						<select name="new_device_id">
								<option selected disabled>Choose Here</option>
								<?php
									foreach($devices as $key=>$value)
									{
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
						</select>
						<label for="device-input">Update Device to:</label>
						<input type="text" name="updated_str" placeholder="Example: Computer"><br>
						<button type="submit" value="submit_new_device" name="submit_new_device">Submit</button>
					</form>
					
				</div>
			</section>
			<section class="new-device-manu" id="manuForms" style="display: none">
				<div class="new-form-container">
					<form method="POST" action="">
						<label for="manufacturers">Select Manufacturer:</label>
						<select name="manufacturer_id">
								<option selected disabled>Choose Here</option>
								<?php
									foreach($manufacturers as $key=>$value)
									{
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
						</select>
						<label for="manufacturer-input">Update Manufacturer to:</label>
						<input type="text" name="updated_str" placeholder="Example: Apple"><br>
						<button type="submit" value="submit_new_manufacturer" name="submit_new_manufacturer">Submit</button>
					</form>
					
				</div>
			</section>
			<section class="new-device-manu" id="serialForms" style="display: none">
				<div class="new-form-container">
					<form method="POST" action="">
						<label for="serial-input">Input Serial Number (exact):</label>
						<input type="text" name="serial_number" placeholder="Example: SN-XXXXX"><br>
						<label for="device-input">Update Serial Number (exact) to:</label>
						<input type="text" name="updated_str" placeholder="Example: SN-XXXX"><br>
						<button type="submit" value="submit_new_serial" name="submit_new_serial">Submit</button>
					</form>
					
				</div>
			</section>
			<section class="status-notifications">
				<div class="parent">
					<?php
						ob_start();
						if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "DeviceExists")
						{
							//make alert css	
							echo "<div class='parent'><div class='errorNotification'><p>Serial number already exists</div></div>";	
						}
					
						if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "Error" && $_REQUEST['val'])
						{
							echo "<div class='parent'>";
							echo "<div class='errorNotification'><p>";
							echo $_REQUEST['val'];
							echo "</p></div>";
							echo "</div>";
						}
					?>
				</div>
			</section>
		</main>
    </body>
	<script>
		//https://www.w3schools.com/howto/howto_js_toggle_hide_show.asp
		//todo: refactor
		function toggleNewForms()
		{
			let buttonName = event.target.name;
			let formName = "";
			
			switch (buttonName) {
				case 'update-device':
					formName = "deviceForms";
					break;
				case 'update-manufacturer':
					formName = "manuForms";
					break;
				case 'update-serial':
					formName = "serialForms";
					break;
				case 'update-equipment':
					formName = "equipmentForms";
					break;
				default:
					console.log("Unknown button name");
					break;
			}
			
			let div = document.getElementById(formName);
            if (div.style.display === "none") {
                div.style.display = "block";
            } else {
                div.style.display = "none";
            }
		}
	</script>
</html>
<?php
ob_start();
if (isset($_POST['submit_new_device']))
{
	$device_id = $_POST['new_device_id'];
	$updated_str = $_POST['updated_str'];
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/update_device?device_id=" . $device_id . "&updated_str=" . $updated_str;
	$result = call_api($url);
	$resultsArray = json_decode($result, true);

    $status = get_msg_status($resultsArray);
    $msg = substr($resultsArray[1], 4); //this should get the msg: line (if it's not json)

    if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=DeviceUpdated"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: update.php?msg=Error&val=$msg");
        die();
    }
}
?>
<?php
ob_start();
if (isset($_POST['submit_new_manufacturer']))
{
	$manufacturer_id = $_POST['manufacturer_id'];
	$updated_str = $_POST['updated_str'];
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/update_manufacturer?manufacturer_id=" . $manufacturer_id . "&updated_str=" . $updated_str;
	$result = call_api($url);
	$resultsArray = json_decode($result, true);

    $status = get_msg_status($resultsArray);
    $msg = substr($resultsArray[1], 4); //this should get the msg: line (if it's not json)

    if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=ManufacturerUpdated"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: update.php?msg=Error&val=$msg");
        die();
    }
}
?>
<?php
ob_start();
if (isset($_POST['submit_new_serial']))
{
	$serial_number = $_POST['serial_number'];
	$updated_str = $_POST['updated_str'];
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/update_serial_number?serial_number=" . $serial_number . "&updated_str=" . $updated_str;
	
	$result = call_api($url);
	$resultsArray = json_decode($result, true);

    $status = get_msg_status($resultsArray);
    $msg = substr($resultsArray[1], 4); //this should get the msg: line (if it's not json)

    if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=SerialUpdated"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: update.php?msg=Error&val=$msg");
        die();
    }
}
?>