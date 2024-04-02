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
					</div>
                </div>
				<div class="parent">
					<p class="error-box"></p>
				</div>
            </section>
        </main>
    </body>
</html>