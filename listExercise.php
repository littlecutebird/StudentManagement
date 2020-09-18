<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Connect to database
require_once "db_config.php";

// If teacher display homework he/she gives, if student display all homework
if ($_SESSION["type"] == "teacher") $sql_query = "SELECT id, title, description, filePath, modified_time, deadline FROM homework where teacherId = ?";
else if ($_SESSION["type"] == "student") $sql_query = "SELECT id, title, description, filePath, modified_time, deadline FROM homework";
else {
    http_response_code(404);
    exit;
}
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
        // Bind variables to prepared SQL statement
        if ($_SESSION["type"] == "teacher") mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            // All homework should display to this user
            $homework_sql_result = $stmt->get_result();

        }
        else {
            echo "Cannot execute SQL homework query";
            exit;
        }
    mysqli_stmt_close($stmt);
}

// If student try to get all submit homework of this student
if ($_SESSION["type"] == "student") {
    $sql_query = "SELECT homeworkId FROM submitHomework WHERE studentId = ?";
    if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
        // Bind variables to prepared SQL statement
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            // All homework should display to this user
            $submitStatus = array();
            $submit_sql_result = $stmt->get_result();
            while ($row = $submit_sql_result -> fetch_assoc()) {
                $submitStatus[$row['homeworkId']] = true;
            }
            
        }
        else {
            echo "Cannot execute SQL submitHomework query";
            exit;
        }
    mysqli_stmt_close($stmt);
}

}

mysqli_close($db_connection);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>List exercise</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel='stylesheet' href='styles/mycss.css'>
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><img src='img/cat-logo.jpg' alt='Cute cat' width='30' height='30'></a>
            </div>
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
                <li class='active'><a href="listExercise.php"><?php if ($_SESSION["type"] == "teacher") echo "Add homework"; else echo "Homework" ?></a></li>
                <li><a href="listChallenge.php"><?php if ($_SESSION["type"] == "teacher") echo "Add challenge"; else echo "Challenge" ?></a></li>
                <li><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="page-header">
        <h1>All exercises</h1>
    </div>
    <?php
    if ($_SESSION["type"] == "teacher") {
        echo "
        <div class='container'>
        <a class='btn btn-success' href='addExercise.php'>Add new homework</a>
        </div>
        ";
    }
    while ($row = $homework_sql_result ->fetch_assoc()) {
        echo " 
        <div class='container'>
            <h2>{$row['title']}</h2>
            <p>Deadline: {$row['deadline']}</p>
        ";
        if ($_SESSION["type"] == "student") {
            $status = isset($submitStatus[$row['id']]) ?'Submitted':'Not done';
            echo "<p>Status: $status</p>";
            echo "<a class='btn btn-success' href='submitHomework.php?homeworkId={$row['id']}'>Submit</a>";
        }
        if ($_SESSION["type"] == 'teacher') {
//            echo "<a class='btn btn-primary' href='editExercise.php?homeworkId={$row['id']}'>Edit</a>";
            echo "<a class='btn btn-danger' href='deleteExercise.php?homeworkId={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this homework?')\">Delete</a>";
        } 
        echo "
        </div>
        ";
    }
    ?>
   

</body>
</html>