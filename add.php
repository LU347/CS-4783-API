<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="index.css">
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
						include("functions.php");
						$dblink=db_connect("equipment");
					
                        $devices_sql="SELECT `auto_id`, `device_type`  FROM `devices` WHERE `status` = 'ACTIVE'";
						$devices_result = fetch_data($dblink, $devices_sql);
						while ($devices_data = $devices_result->fetch_array(MYSQLI_ASSOC)) 
                        {
                            $devices[$devices_data['auto_id']]=$devices_data['device_type'];
                        }
					
						$manu_sql = "SELECT `auto_id`, `manufacturer` FROM `manufacturers` WHERE `status` = 'ACTIVE'";
						$manu_result = fetch_data($dblink, $manu_sql);
						while ($manu_data = $manu_result->fetch_array(MYSQLI_ASSOC)) 
                        {
                            $manufacturers[$manu_data['auto_id']]=$manu_data['manufacturer'];
                        }
						
						if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "DeviceExists")
						{
							//make alert css	
							echo "<div class='parent'>Serial Number already exists</div>";		
						}
					?>
					<div class="form-container">
						<form method="post" action="">
							<label for="devices">Device Type:</label>
							<select id="devices" name="devices">
								<option selected disabled>Choose Here</option>
								<?php
									foreach($devices as $key=>$value)
									{
										echo "<option value=" . $key . ">" . $value . "</option>";
									}
								?>
							</select>

							<label for="manufacturer">Manufacturer:</label>
							<select id="manufacturer" name="manufacturer">
								<option selected disabled>Choose Here</option>
								<?php
									foreach($manufacturers as $key=>$value)
									{
										echo "<option value=" . $key . ">" . $value . "</option>";
									}
								?>
							</select>
							
							<label for="serialNumber">Serial Number:</label>
							<input type="text" id="serialNumber" name="serialNumber" placeholder="Format: SN-090912309asd"><br>
							<input type="submit" value="SUBMIT" id="submit">
						</form>
						<div class="parent">
							<p>[Submit button will be disabled until the reimport is done]</p>
						</div>
					</div>
                </div>
				<div class="parent">
					<p class="error-box"></p>
				</div>
            </section>
        </main>
    </body>
</html>
<?php
	if (isset($_POST['submit']))
	{
		$device = $_POST['device'];
		$manufacturer = $_POST['manufacturer'];
		$serialNumber = trim($_POST['serial']);
		$sql = "SELECT `auto_id` FROM `serials` WHERE `serial_number`='$serialNumber'";
		$result = $dblink->query($sql) or
			die("<p>Error occured with $sql<p>".$dblink->error);
		if ($result->num_rows<=0)
		{
			//not running this until the reimport is done
			/*
			$sql = "INSERT INTO `serials` (`device_id`, `manufacturer_id`, `serial_number`)
					VALUES ('$device','$manufacturer', '$serialNumber')";
			$dblink->query($sql) or
				die("<p>Error occured with $sql<p>".$dblink->error);
			header("Location index.php?msg=EquipmentAdded");
			*/	
		}
		else
		{
			header("Location add.php?msg=DeviceExists");
		}
	}