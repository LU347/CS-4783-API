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
							<p>Input <strong>old</strong> equipment details:</p>
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
								
								<p>Input <strong>new</strong> equipment details:</p>
								
								<label for="device_id">Device Type:</label><br>
								<select name="new_device_id">
									<option selected disabled>Choose Here</option>
                                    <?php
										foreach($devices as $key=>$value)
										{
											echo '<option value="'.$key.'">'.$value.'</option>';
										}
                                    ?>
								</select>
								<label for="manufacturer_id">Manufacturer:</label><br>
								<select name="new_manufacturer_id">
									<option selected disabled>Choose Here</option>
									<?php
										foreach($manufacturers as $key=>$value)
										{
											echo '<option value="'.$key.'">'.$value.'</option>';
										}
									?>
								</select>
								<label for="new_serial_number">Serial Number:</label><br>
								<input type="text" name="serial_number" id="serialInput" placeholder="Format: SN-xxxxx..">
								
								<button type="submit" value="submit-search" name="submit-search">Search</button>
							</form>
						</div>
					</div>
				</div>
            </section>
			<section class="status-notifications">
				<div class="parent">
					<?php
					ob_start();
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
</html>
