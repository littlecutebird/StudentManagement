<!DOCTYPE html>
<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Only teacher can add exercise!
if ($_SESSION["type"] != "teacher") {
    http_response_code(404);
    exit;
}
// Connect to database
require_once "db_config.php";

// Upload file
require_once "upload.php";

if (empty($upload_err) && isset($_POST["submit"])) {
    // Update database homework: id, teacherId, title, description, filePath, modified_time, deadline
    $insert_query = "INSERT INTO homework SET teacherId = {$_SESSION["id"]}, title = ?, description = ?, filePath = ?, modified_time = now(), deadline = ?";
    if ($stmt = mysqli_prepare($db_connection, $insert_query)) {
            // Bind variables to prepared SQL statement
            mysqli_stmt_bind_param($stmt, "ssss", $_POST['title'], $_POST["description"], $target_file, $_POST["deadline"]);

            // Execute SQL statement
            if (mysqli_stmt_execute($stmt)) {
                $upload_err = "Upload success";
            }
            else {
                echo "Cannot execute SQL query";
                exit;
            }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($db_connection);

?>

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
                    <li><a href="listUser.php">List user</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class="page-header">
            <h1>Add new exercise</h1>
        </div>
        <div class='container'>
            <form action='' method='post' enctype='multipart/form-data'>
                <div class='form-group'>
                    <label for='title'>Title:</label>
                    <input type='text' id='title' name='title' required><br>
                </div>
                <div class='form-group'>
                    <label for='description'>Description</label>
                    <textarea id='description' name='description' placeholder='Enter description here' required></textarea><br>
                </div>
                <div class='form-group'>
                    <label for='deadline'>Deadline:</label>
                    <input type='datetime-local' id='deadline' name='deadline' required> <br>
                </div>
                <div class='form-group'>
                    <label for='fileToUpload'>Select file to upload:</label>
                    <input type="file" name="fileToUpload" id="fileToUpload" required> <br>
                </div>
                <div class='form-group'>
                    <button type="submit" class="btn btn-success" value="Upload File" name="submit">Add new homework</button>  
                    <button class='btn btn-warning' type='reset'>Reset</button>
                    <a class='btn btn-primary' href='listExercise.php'>Cancel</a>
                </div>
                <span class="form-text text-muted"><?php echo $upload_err; ?></span> 
               
            </form>
            
        </div>
       
    
    </body>
</html>