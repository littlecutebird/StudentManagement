<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check permission, only teacher can delete student account
if (!isset($_SESSION["type"]) || $_SESSION["type"] != "teacher") {
    http_response_code(404);
    exit("Don't have permission to delete account");
}

// Connect to database
require_once "db_config.php";

// Check account trying to delete is student account
$sql_query = "SELECT type FROM account WHERE username = ?";
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    // Bind variables to prepared SQL statement
    mysqli_stmt_bind_param($stmt, "s", $_GET['username']);

    // Execute SQL statement
    if (mysqli_stmt_execute($stmt)) {
       $sql_result = $stmt ->get_result();
       $row = $sql_result -> fetch_assoc();
       if ($row['type'] != 'student') {
           http_response_code(404);
           exit("Don't have permission to delete account");
       }
    }
    else {
        exit("Cannot execute SQL delete account query");
    }
    mysqli_stmt_close($stmt);
}

// Delete account
$sql_query = "DELETE FROM account WHERE username = ?";
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    // Bind variables to prepared SQL statement
    mysqli_stmt_bind_param($stmt, "s", $_GET['username']);

    // Execute SQL statement
    if (mysqli_stmt_execute($stmt)) {
       header("location: listUser.php");
    }
    else {
        exit("Cannot execute SQL delete account query");
    }
    mysqli_stmt_close($stmt);
}

// Close db connection
mysqli_close($db_connection);
?>
 