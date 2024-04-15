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
					<div class="new-device-manu-grid">
						<em>Need to add a new device or manufacturer?</em>
						<button onclick="toggleNewForms()">Click Here to Add a New Device or Manufacturer</button>
					</div>
				</div>
            </section>
			<section class="new-device-manu" id="newForms" style="display: none">
				<div class="new-form-container">
					<form method="POST" action="">
						<label for="devices">New Device:</label>
						<input type="text" name="new_device_id" placeholder="Example: Computer"><br>
						<button type="submit" value="submit_new_device" name="submit_new_device">Submit</button>
					</form>
					
					<form method="POST" action="">
						<label for="devices">New Manufacturer:</label>
						<input type="text" name="new_manufacturer" placeholder="Example: Apple"><br>
						<button type="submit" value="submit_new_manufacturer" name="submit_new_manufacturer">Submit</button>
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
</html>
<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

    if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=EquipmentAdded");
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: add.php?msg=Error&val=$msg");
        die();
    }

}

if (isset($_POST['submit_new_device']))
{
    $device_id = $_POST['new_device_id'];
    $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/add_device?device_id=" . $device_id;
    $result = call_api($url);
    $resultsArray = json_decode($result, true);

    $status = get_msg_status($resultsArray);
    $msg = substr($resultsArray[1], 4); //this should get the msg: line (if it's not json)

    if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=DeviceAdded"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: add.php?msg=Error&val=$msg");
        die();
    }
}

if (isset($_POST['submit_new_manufacturer']))
{
    $new_manufacturer = $_POST['new_manufacturer'];
    $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/add_manufacturer?manufacturer_id=" . $new_manufacturer;

    $result = call_api($url);
    $resultsArray = json_decode($result, true);

    $status = get_msg_status($resultsArray);
    $msg = substr($resultsArray[1], 4); //this should get the msg: line (if it's not json)

    if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=ManufacturerAdded"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: add.php?msg=Error&val=$msg");
        die();
    }
}
?>
