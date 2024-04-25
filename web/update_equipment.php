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
            </ul>
        </nav>
        <main>
            <section class="search-page" id="searchPage">
                <div class="parent">
                    <h1>Update Equipment</h1>
                </div>
				<div class="search-parent">
					<div class="search-grid">
						<div class="form-container">
							<h2><em>strict search so, device && manufacturer && serial</em></h2>
							<form method="POST" action="">
								<label for="device_id">Device Type:</label><br>
								<select name="device_id">
									<option selected disabled>Choose Here</option>
								</select>
								<label for="manufacturer_id">Manufacturer:</label><br>
								<select name="manufacturer_id">
									<option selected disabled>Choose Here</option>
								</select>
								<label for="serial_number">Serial Number:</label><br>
								<input type="text" name="serial_number" id="serialInput" placeholder="Format: SN-xxxxx..">
								<button type="submit" value="submit-search" name="submit-search">Search</button>
							</form>
						</div>
						<div class="form-container">
							<form method="POST" action="">
								<label for="device_id">Search by Device Type:</label>
								<select name="device_id">
									<option selected disabled>Choose Here</option>
								</select>
								<button type="submit" value="submit-search-device" name="submit-search-device">Search Device</button>
							</form>
							<br>
							<form method="POST" action="">
								<label for="manufacturer_id">Search by Manufacturer:</label>
								<select name="manufacturer_id">
									<option selected disabled>Choose Here</option>
								</select>
								<button type="submit" value="submit-search-manufacturer" name="submit-search-manufacturer">Search Manufacturer</button>
							</form>
							<br>
							<form method="POST" action="">
								<label for="serial_number">Search by Serial Number:</label>
								<input type="text" name="serial_number" placeholder="Format: SN-XXXXXXXXXX..">
								<button type="submit" value="submit-search-serial" name="submit-search-serial">Search Serial Number</button>
							</form>
						</div>
					</div>
				</div>
            </section>
			<section class="status-notifications">
				<div class="parent">
				</div>
			</section>
		</main>
    </body>
</html>
