<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Connect to database
require_once 'db_config.php';

// Get info of all user in table account
$sql_query = 'SELECT * FROM account';
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
     
        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            $sql_result = $stmt->get_result();
        }
        else {
            exit("Cannot execute SQL query");
        }
    mysqli_stmt_close($stmt);
}

mysqli_close($db_connection);
// End interact with database
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel='stylesheet' href='styles/mycss.css'>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><img src='img/cat-logo.jpg' alt='Website logo' width='30' height='30'></a>
            </div>
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li><a href="listExercise.php"><?php if ($_SESSION["type"] == "teacher") echo "Add homework"; else echo "Homework" ?></a></li>
                <li><a href="listChallenge.php"><?php if ($_SESSION["type"] == "teacher") echo "Add challenge"; else echo "Challenge" ?></a></li>
                <li class='active'><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="page-header">
        <h1>List user in this website</h1>
    </div>
    <div class="container panel-group">
    <?php
    while ($row = $sql_result -> fetch_assoc()) {
        echo "
        <div class='panel panel-success'>
            <div class='panel-heading'>Full name: {$row['fullname']} </div>
            <div class='panel-body'>Email: {$row['email']} </div>
            <div class='panel-body'>Phone number: {$row['phoneNumber']} </div>
            <div class='panel-body'>Account type: {$row['type']} </div>
            <div class='panel-body'><a class='btn btn-danger' href='sendMsg.php?userId={$row['id']}'>Message me</a></div>
        </div>
        ";
    }
    ?>
    </div>
   
</body>
</html>