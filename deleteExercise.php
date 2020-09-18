<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Connect to database
require_once "db_config.php";

// Get url to deleted file
$sql_query = "SELECT filePath FROM homework WHERE id = ?";
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    // Bind variables to prepared SQL statement
    mysqli_stmt_bind_param($stmt, 'i', $_GET['homeworkId']);

    // Execute SQL statement
    if (mysqli_stmt_execute($stmt)) {
        $res = $stmt->get_result();
        $row = $res ->fetch_assoc();
        $deleteFilePath = $row['filePath'];
    }
    else {
        echo "Cannot execute select SQL query";
        exit;
    }
    mysqli_stmt_close($stmt);
}

// Delete from database
$sql_query = "DELETE FROM homework WHERE id = ? AND teacherId = ?";

if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    // Bind variables to prepared SQL statement
    mysqli_stmt_bind_param($stmt, 'ii', $_GET['homeworkId'], $_SESSION['id']);
   
    // Execute SQL statement
    if (mysqli_stmt_execute($stmt)) {
        $res = $stmt->get_result();
        // Remove file from folder uploads/
        unlink($deleteFilePath);
    }
    else {
        echo "Cannot execute delete SQL query";
        exit;
    }
    mysqli_stmt_close($stmt);
}




mysqli_close($db_connection);

header('location: listExercise.php');

?>
