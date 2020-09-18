<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Connect to database
require_once "db_config.php";

// If teacher display challenge he/she gives, if student display all challenge
if ($_SESSION["type"] == "teacher") $sql_query = "SELECT id, title, description, filePath, modified_time, deadline FROM challenge where teacherId = ?";
else if ($_SESSION["type"] == "student") $sql_query = "SELECT id, title, description, filePath, modified_time, deadline FROM challenge";
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
            $challenge_sql_result = $stmt->get_result();
        }
        else {
            echo "Cannot execute SQL homework query";
            exit;
        }
    mysqli_stmt_close($stmt);
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
                <li><a href="listExercise.php"><?php if ($_SESSION["type"] == "teacher") echo "Add homework"; else echo "Homework" ?></a></li>
                <li class='active'><a href="listChallenge.php"><?php if ($_SESSION["type"] == "teacher") echo "Add challenge"; else echo "Challenge" ?></a></li>
                <li><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="page-header">
        <h1>All Challenges</h1>
    </div>
    
    <!--Button to add challenge for teacher--> 
    <?php
    if ($_SESSION["type"] == "teacher") {
        echo "
        <div class='container'>
        <a class='btn btn-success' href='addChallenge.php'>Add new challenge</a>
        </div>
        ";
    }
    
    while ($row = $challenge_sql_result ->fetch_assoc()) {
        echo " 
        <div class='container'>
            <h2>{$row['title']}</h2>
            <p>Hint: {$row['description']}</p>
            <p>Deadline: {$row['deadline']}</p>
        ";
        if ($_SESSION["type"] == "student") {
            echo "<a class='btn btn-danger' href='submitChallenge.php?challengeId={$row['id']}'>Submit</a>";
        }
        if ($_SESSION["type"] == 'teacher') {
//            echo "<a class='btn btn-primary' href='editChallenge.php?challengeId={$row['id']}'>Edit</a>";
            echo "<a class='btn btn-danger' href='deleteChallenge.php?challengeId={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this homework?')\">Delete</a>";
        } 
        echo "
        </div>
        ";
    }
    ?>
   

</body>
</html>