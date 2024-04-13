<!DOCTYPE html>
    <head>
        <title></title>
        <link rel="stylesheet" href="index.css">
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
							//make alert css	
							echo "<div class='parent'><div class='successNotification'><p>Equipment successfully added!</div></div>";		
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
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>