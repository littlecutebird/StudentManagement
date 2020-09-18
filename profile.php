<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
// Init session
session_start();

// Start interact with database
require_once 'db_config.php';

$sql_query = "SELECT fullname, email, phoneNumber, type FROM account where username = ?";
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username = $_GET["username"];
   
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $fullname, $email, $phonenumber, $type);
        
        if (mysqli_stmt_fetch($stmt)) {
            
        }
    }
    else {
        echo "Cannot execute SQL query";
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($db_connection);
// End interact with database

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Profile</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>

    <div class="container">
        <h1>Profile</h1>
        <p>Full name: <?php echo $fullname; ?></p>
        <p>Email: <?php echo $email; ?></p>
        <p>Phone number: <?php echo $phonenumber; ?></p>
        <p>Account type: <?php echo $type; ?></p>
    </div>
    
    <div class="container">
        <a href="editProfile.php" class="btn btn-primary">Edit profile</a>
    </div>

</body>

