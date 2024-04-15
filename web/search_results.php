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
            <section class="search-results-page">
                <div class="parent">
                    <h1>Search Results</h1>
                </div>
				<div class="search-results-container">
					Populate search results here, each having their own div?
					<div class="container">
						<?php
						  ob_start();
						  if (isset($_REQUEST['searchResults']))
						  {
							  echo "<pre>";
							  echo print_r($_REQUEST['data']);
							  echo "</pre>";
						  }
						 ?>
					</div>
				</div>
            </section>
        </main>
    </body>
</html>