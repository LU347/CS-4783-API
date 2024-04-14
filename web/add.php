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
                <li><a href="">Add Equipment</a></li>
            </ul>
        </nav>
        <main>
            <section class="add-device">
                <div class="parent">
					<h1>Add Items</h1>
				</div>
                <div class="parent">
					<?php
						include("../api/api_functions.php");
						$result = call_api("https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_devices");
						$resultsArray = json_decode($result, true);
						$devices = get_msg_data($resultsArray);
						
						$result = call_api("https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/list_manufacturers");
						$resultsArray = json_decode($result, true);
						$manufacturers = get_msg_data($resultsArray);
					?>
					<div class="form-container">
						<form method="POST" action="">
							<label for="devices">Device Type:</label>
							<select name="device_id">
								<option selected disabled>Choose Here</option>
								<?php
									foreach($devices as $key=>$value)
									{
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
							</select>

							<label for="manufacturer">Manufacturer:</label>
							<select name="manufacturer_id">
								<option selected disabled>Choose Here</option>
								<?php
									foreach($manufacturers as $key=>$value)
									{
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
							</select>
							
							<label for="serialNumber">Serial Number:</label>
							<input type="text" id="serialInput" name="serial_number" placeholder="Format: SN-090912309asd"><br>
							<button type="submit" value="submit" name="submit">Submit Equipment</button>
						</form>
					</div>
                </div>
				<div class="parent">
					<em><a href="">Need to add a new device or manufacturer? Click here.</a></em>
				</div>
				<div class="parent">
					<?php
						if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "DeviceExists")
						{
							//make alert css	
							echo "<div class='parent'><div class='errorNotification'><p>Serial number already exists</div></div>";	
						}
					?>
				</div>
            </section>
		</main>
    </body>
</html>
<?php
	if (isset($_POST['submit']))
	{
		$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/add_equipment";
        $device = $_POST['device_id'];
        $manufacturer = $_POST['manufacturer_id'];
        $serialNumber = trim($_POST['serial_number']);

        $newUrl = $url . "?device_id=" . $device . "&manufacturer_id=" . $manufacturer . "&serial_number=" . $serialNumber;
        $result = call_api($newUrl);
        $resultsArray = json_decode($result, true);
        $tmp = $resultsArray[0];
        $status = explode("Status:", $tmp);
		$status[1] = trim($status[1]);
		
		if (strcmp($status[1], "Success") == 0) {
			header("Location: index.php?msg=EquipmentAdded");
			exit();
		}
	}
?>