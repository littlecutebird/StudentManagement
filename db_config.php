<?php
// Database credential
define("DB_SERVER", 'localhost:3307');
define("DB_USERNAME", 'root');
define("DB_PASSWORD", '');
define("DB_NAME", "studentManagement");

// Connect to db
$db_connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$db_connection) {
    die("ERROR: could not connect".mysqli_connect_error());
}

?>