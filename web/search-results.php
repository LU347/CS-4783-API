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
            </ul>
        </nav>
        <main>
            <section class="home-page">
				<?php
                  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "EquipmentAdded")
                  {
                      echo "<div class='parent'><div class='successNotification'><p>Equipment successfully added!</div></div>";		
                  }
				  
                  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "DeviceAdded")
                  {
                      echo "<div class='parent'><div class='successNotification'><p>Device successfully added!</div></div>";		
                  }
				
				  if (isset($_REQUEST['msg']) && $_REQUEST['msg'] == "ManufacturerAdded")
                  {
                      echo "<div class='parent'><div class='successNotification'><p>Manufacturer successfully added!</div></div>";		
                  }
				 ?>
                <div class="parent">
                    <h1>Search Results</h1>
                </div>
            </section>
        </main>
    </body>
</html>