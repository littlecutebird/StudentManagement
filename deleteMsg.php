<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Connect to database
require_once "db_config.php";

// Delete from database
$sql_query = "DELETE FROM message WHERE id = ?";

if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    // Bind variables to prepared SQL statement
    mysqli_stmt_bind_param($stmt, 'i', $_GET['messageId']);
   
    // Execute SQL statement
    if (mysqli_stmt_execute($stmt)) {
       
    }
    else {
        echo "Cannot execute delete msg SQL query";
        exit;
    }
    mysqli_stmt_close($stmt);
}


mysqli_close($db_connection);

header("location: {$_SERVER['HTTP_REFERER']}");

?>
