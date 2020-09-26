<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
// Connect to database
require_once "db_config.php";

// Query information about this homeworkId
$homework_sql_query = "SELECT id, title, description, filePath, modified_time, deadline FROM homework where id = ?";


if ($stmt = mysqli_prepare($db_connection, $homework_sql_query)) {
        // Bind variables to prepared SQL statement
      mysqli_stmt_bind_param($stmt, "i", $_GET["homeworkId"]);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            $homework_sql_result = $stmt->get_result();
            if (!($homework = $homework_sql_result ->fetch_assoc())) {
                exit("Homework don't exist!");
            }
            // now $homework store all info about this homework, such as $homework['filePath'], $homework['title']
        }
        else {
            exit("Cannot execute SQL query");
        }
    mysqli_stmt_close($stmt);
}

// Query all submit homework of this homework

$submitHomework_sql_query = "SELECT studentId, fullname, filePath, submit_time FROM submitHomework s INNER JOIN account a on s.studentId = a.id WHERE homeworkId = ?";
if ($stmt = mysqli_prepare($db_connection, $submitHomework_sql_query)) {
        // Bind variables to prepared SQL statement
      mysqli_stmt_bind_param($stmt, "i", $_GET["homeworkId"]);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            $submitHomework_sql_result = $stmt->get_result();
            // now call $submitHomework -> fetch_assoc() to get info about every submission, such as $submitHomework['filePath'], $submitHomework['studentId']
        }
        else {
            exit("Cannot execute SQL query");
        }
    mysqli_stmt_close($stmt);
}

mysqli_close($db_connection);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>See submissions</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
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
                <li class="active"><a href="listExercise.php"><?php if ($_SESSION['type'] == 'teacher') echo 'Add homework'; else echo 'Homework';?></a></li>
                <li><a href="listChallenge.php"><?php if ($_SESSION['type'] == 'teacher') echo 'Add challenge'; else echo 'Challenge';?></a></li>
                <li><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="page-header">
        <h1>See student's submissions</h1>
    </div>
    <div class='container'>
        <div class="panel panel-success">
            <div class="panel-heading"><?php echo $homework['title']; ?></div>
            <div class='panel-body'><?php echo $homework['description']; ?></div>
            <div class="panel-body"><a role='button' class='btn btn-warning' href='<?php echo $homework['filePath']; ?>'>Statement</a></div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">Student submissions</div>
            <div class="panel-body">
            <?php
            while ($row = $submitHomework_sql_result -> fetch_assoc()) {
                echo "
                <div class='panel panel-info'>
                    <div class='panel-heading'>{$row['fullname']}</div>
                    <div class='panel-body'>Submit time: {$row['submit_time']}</div>
                    <div class='panel-body'><a role='button' class='btn btn-warning' href='{$row['filePath']}'>File submission</a></div>
                </div>
                ";
            }
            ?>    
            </div>
           
        </div>
    </div>
</body>
</html>