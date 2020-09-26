<?php
// Init session
session_start();

// Start interact with database
require_once 'db_config.php';

if ($_SESSION['username'] != $_GET['username']) {
    exit("Why you here I don't allow you!");
}
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
  <link rel="stylesheet" href="styles/mycss.css">
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><img src='img/cat-logo.jpg' alt='website logo' height='30' width='30'></a>
            </div>
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li><a href="listExercise.php"><?php if ($_SESSION['type'] == 'teacher') echo 'Add homework'; else echo 'Homework';?></a></li>
                <li><a href="listChallenge.php"><?php if ($_SESSION['type'] == 'teacher') echo 'Add challenge'; else echo 'Challenge';?></a></li>
                <li><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="page-header">
        <h1>My profile</h1>
    </div>
    <div class='container'>
        <div class="panel panel-success">
            <div class="panel-heading">Full name: <?php echo $fullname; ?></div>
            <div class="panel-body">Email: <?php echo $email; ?></div>
            <div class="panel-body">Phone number: <?php echo $phonenumber; ?></div>
            <div class="panel-body">Account type: <?php echo $type; ?></div>
        </div>
    </div>

    <div class="container">
        <a href="editProfile.php?username=<?= $_SESSION['username']?>" class="btn btn-primary">Edit profile</a>
        <a href="changePassword.php?username" class="btn btn-info">Change password</a>
    </div>

</body>

