<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if ($_SESSION["type"] != "student") {
    http_response_code(404);
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
            if (!($row = $homework_sql_result ->fetch_assoc())) {
                echo "Homework don't exist!";
                exit;
            }
        }
        else {
            echo "Cannot execute SQL query";
            exit;
        }
    mysqli_stmt_close($stmt);
}



// Upload submit homework
require_once "upload.php";

if (isset($_POST["submit"])) {
    // Update database submitHomework: id, homeworkId, studentId, filePath, submit_time
    $insert_query = "INSERT INTO submitHomework SET homeworkId = ?, studentId = ?, filePath = ?, submit_time = now()";
    if ($stmt = mysqli_prepare($db_connection, $insert_query)) {
            // Bind variables to prepared SQL statement
            mysqli_stmt_bind_param($stmt, "iis", $_GET["homeworkId"], $_SESSION["id"], $target_file);

            // Execute SQL statement
            if (mysqli_stmt_execute($stmt)) {
                $upload_err = 'Upload homework success';
            }
            else {
                exit("Cannot execute SQL query");
            }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($db_connection);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Submit homework</title>
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
                    <li class="active"><a href="listExercise.php"><?php if ($_SESSION["type"] == "teacher") echo "Add homework"; else echo "Homework" ?></a></li>
                    <li><a href="listChallenge.php"><?php if ($_SESSION["type"] == "teacher") echo "Add challenge"; else echo "Challenge" ?></a></li>
                    <li><a href="#">Page 3</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class='page-header'>
            <h1>Submit homework</h1>
        </div>
        <div class='container'>
            <div class='row'>
                <div class='col-lg-6'>
                    <div class='panel panel-primary'>
                        <div class="panel-heading"><?= $row['title'] ?></div>
                        <div class="panel-body"><i><?= $row['description'] ?></i></div>
                        <div class='panel-body'>Last modified time: <?= $row['modified_time'] ?></div>
                        <div class='panel-body'>Deadline: <?= $row['deadline'] ?></div>
                        <div class='panel-body'><a class='btn btn-warning' href='<?= $row['filePath'] ?>'>Statement</a></div>
                    </div>
                </div>
                <div class='col-lg-6'>
                    <br>
                    <form action='' method='post' enctype='multipart/form-data'>
                        <div class='form-group'>
                            <label for='fileToUpload'>Select file to upload:</label>
                            <input class='form-control' type="file" name="fileToUpload" id="fileToUpload" required>
                        </div>
                        <input class='btn btn-success' type="submit" value="Submit homework" name="submit">

                        <span class="help-block"><?php echo $upload_err; ?></span> 

                    </form>
        
                </div>
            </div>
           
        </div>
       <!--<a class='btn btn-primary' href='listExercise.php'>Back</a>-->
    
    </body>
</html>