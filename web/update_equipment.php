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
				<li><a href="view.php">View Equipment</a></li>
            </ul>
        </nav>
        <main>
			<section class="status-notifications">
				<div class="parent">
					<?php
					ob_start();
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
            <section class="update-equipment" id="updateEquipment">
                <div class="parent">
                    <h1>Update Equipment</h1>
                </div>
				<div class="parent">
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
						<div class="form-container">
							<form method="POST" class="form" action="">
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
								<input type="text" name="serial_number" id="serialInput" placeholder="Format: SN-xxxxx..">
								<button type="submit" value="submit" name="submit">Search</button>
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
if (isset($_POST['submit']))
{
    $url = "https://ec2-18-220-186-80.us-east-2.compute.amazonaws.com/api/update_equipment?";
	$device_id = $_REQUEST['device_id'];
    $manufacturer_id = $_REQUEST['manufacturer_id'];
    $serial_number = $_REQUEST['serial_number'];
	$newUrl = $url . "device_id=" . $device_id . "&manufacturer_id=" . $manufacturer_id . "&serial_number=" . $serial_number;
	
    $result = call_api($newUrl);
    $resultsArray = json_decode($result, true);

    $status = get_msg_status($resultsArray);
    $msg = substr($resultsArray[1], 4); //this should get the msg: line (if it's not json)

    if (strcmp($status, "Success") == 0) 
    {
        header("Location: index.php?msg=EquipmentUpdated"); // change to device added
        die();
    }

    if (strcmp($status, "ERROR") == 0) 
    {
        header("Location: update_equipment.php?msg=Error&val=$msg");
        die();
    }
}
?>
