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
						<select name="device_id">
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
						<input type="text" name="updated-manufactrer" placeholder="Example: Computer"><br>
						<button type="submit" value="submit_new_manufacturer" name="submit_new_manufacturer">Submit</button>
					</form>
					
				</div>
			</section>
			<section class="new-device-manu" id="serialForms" style="display: none">
				<div class="new-form-container">
					<form method="POST" action="">
						<label for="serial-input">Input Serial Number (exact):</label>
						<input type="text" name="serial-number" placeholder="Example: Computer"><br>
						<label for="device-input">Update Serial Number (exact) to:</label>
						<input type="text" name="updated-manufactrer" placeholder="Example: Computer"><br>
						<button type="submit" value="submit_new_manufacturer" name="submit_new_manufacturer">Submit</button>
					</form>
					
				</div>
			</section>
		</main>
    </body>
	<script>
		//https://www.w3schools.com/howto/howto_js_toggle_hide_show.asp
		//todo: refactor
		function toggleNewForms()
		{
			console.log(event.target.name);
			let buttonName = event.target.name;
			if (buttonName == "update-device")
            {
              let div = document.getElementById("deviceForms");
              if (div.style.display === "none") {
                  div.style.display = "block";
              } else {
                  div.style.display = "none";
              }
            } else if (buttonName == "update-manufacturer") {
              let div = document.getElementById("manuForms");
              if (div.style.display === "none") {
                  div.style.display = "block";
              } else {
                  div.style.display = "none";
              }
            } else if (buttonName == "update-serial") {
			  let div = document.getElementById("serialForms");
              if (div.style.display === "none") {
                  div.style.display = "block";
              } else {
                  div.style.display = "none";
              }
            }
		}
	</script>
</html>
<?php
if (isset($_POST['submit_new_device']))
{
	$device_id = $_POST['device_id'];
	$updated_str = $_POST['updated_str'];
	$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/update_device?device_id=" . $device_id . "&updated_str=" . $updated_str;
	echo $url;
	die();
}
?>