<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <nav>
            <ul class="navbar">
                <li><a href="">Home</a></li>
                <li><a href="">Search Equipment</a></li>
                <li><a href="">Add Equipment</a></li>
            </ul>
        </nav>
        <main>
            <h1 style="color:red">TODO: separate these into their own pages</h1>
            <section class="home-page">
                <div class="container">
                    <div class="home-grid">
                        <div class="card">
                            <h3>Search Equipment</h3>
                            <p><em>Search equipment by their device type, manufacturer, or serial number</em></p>
                            <button name="search-button">Click Here</button>
                        </div>
                        <div class="card">
                            <h3>Add Equipment</h3>
                            <p><em>Add equipment with a valid device type, manufacturer, and serial number</em></p>
                            <button name="add-button">Click Here</button>
                        </div>
                    </div>
                </div>
            </section>
            <hr>
            <section class="add-device">
                <h1>Add Equipment</h1>
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
						/*
                        $result=$dblink->query($sql) or
                            die("<p>Error occured with $sql<br></p>".$dblink->error);
                        $devices=array();
                        $manufacturers=array();
                        while ($data=$result->fetch_array(MYSQLI_ASSOC)) 
                        {
                            $devices[$data['auto_id']]=$data['device_type'];
                        }
						*/
					?>
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
            </section>
            <hr>
            <section class="search-equipment">
                <h1>Search Equipment</h1>
            </section>
        </main>
    </body>
</html>