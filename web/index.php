<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="../index.css">
    </head>
    <body>
        <nav>
            <ul class="navbar">
                <li><a href="">Home</a></li>
                <li><a href="search.php">Search Equipment</a></li>
                <li><a href="add.php">Add Equipment</a></li>
				<li><a href="update.php">Update Equipment</a></li>
            </ul>
        </nav>
        <main>
            <section class="home-page">
				<?php
				  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "DeviceUpdated")
				  {
					  echo "<div class='parent'><div class='successNotification'><p>Device successfully updated!</div></div>";
				  }
				  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "ManufacturerUpdated")
				  {
					  echo "<div class='parent'><div class='successNotification'><p>Manufacturer successfully updated!</div></div>";
				  }
				  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "SerialUpdated")
				  {
					  echo "<div class='parent'><div class='successNotification'><p>Serial Number successfully updated!</div></div>";
				  }
				
                  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "EquipmentAdded")
				  {
					  echo "<div class='parent'><div class='successNotification'><p>Equipment successfully updated!</div></div>";
				  }
				
                  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "DeviceAdded")
				  {
					  echo "<div class='parent'><div class='successNotification'><p>Device successfully updated!</div></div>";
				  }
				  
			      if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "ManufacturerAdded")
				  {
					  echo "<div class='parent'><div class='successNotification'><p>Manufacturer successfully updated!</div></div>";
				  }
				 ?>
                <div class="parent">
                    <div class="home-grid">
                        <div class="card">
                            <h3>Search Equipment</h3>
                            <p><em>Search equipment by their device type, manufacturer, or serial number</em></p>
                            <a href="search.php">
								<button name="search-button">Go to Search Page</button>
							</a>
                        </div>
                        <div class="card">
                            <h3>Add Equipment</h3>
                            <p><em>Add equipment with a valid device type, manufacturer, and serial number</em></p>
                            <a href="add.php">
								<button name="add-button">Go to Add Page</button>
							</a>
                        </div>
						<div class="card">
                            <h3>Update Equipment</h3>
                            <p><em>Update equipment with a valid device type, manufacturer, and serial number</em></p>
                            <a href="update.php">
								<button name="update-button">Go to Update Page</button>
							</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>