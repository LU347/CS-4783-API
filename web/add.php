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
</html>
<?php
	if (isset($_POST['submit']))
	{
		$url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/add_equipment";
        $device = $_POST['device_id'];
        $manufacturer = $_POST['manufacturer_id'];
        $serialNumber = trim($_POST['serial_number']);

        $newUrl = $url . "?device_id=" . $device . "&manufacturer_id=" . $manufacturer . "&serial_number=" . $serialNumber;	//concatenates args
        $result = call_api($newUrl);	//calls add_equipment 
        $resultsArray = json_decode($result, true); //turns result into array
		$status = get_msg_status($resultsArray);
  		$msg = substr($resultsArray[1], 4); //this should get the msg: line (if it's not json)
		
		if (strcmp($status, "Success") == 0) {
			header("Location: index.php?msg=EquipmentAdded");
			exit();
		}
		
		if (strcmp($status, "ERROR") == 0) {
			header("Location: add.php?msg=Error&val=$msg");
		}
		//need to handle error
	}
?>