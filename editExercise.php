<!DOCTYPE html>

<?php
exit("Do not have this function yet");
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

// Get all info about this homework
$sql_query = "SELECT title, description, filePath, deadline FROM homework where id = ? and teacherId = ?";
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
        // Bind variables to prepared SQL statement
        mysqli_stmt_bind_param($stmt, "ii", $_GET["homeworkId"], $_SESSION["id"]);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            $homework_sql_result = $stmt->get_result();
            if (!mysqli_num_rows($homework_sql_result)) {
                // don't have right permission to access this edit page
                exit("Don't exist");
            }
            else {
                // access homeworkInfo['title] to access title of homework, ...
               $homeworkInfo = $homework_sql_result -> fetch_assoc();
            }
        }
        else {
            echo "Cannot execute SQL homework query";
            exit;
        }
    mysqli_stmt_close($stmt);
}


mysqli_close($db_connection);

?>

<html>
    <head>
        <title>Edit homework</title>
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
                    <li class="active"><a href="listExercise.php">Add homework</a></li>
                    <li><a href="#">Page 2</a></li>
                    <li><a href="#">Page 3</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class="page-header">
            <h1>Edit homework</h1>
        </div>
        <div class='container'>
           <form action='' method='post' enctype='multipart/form-data'>
               <div class='form-group'>
                   <label for='title'>Title:</label>
                   <input type='text' id='title' name='title' value="<?php echo $homeworkInfo['title']; ?>"><br>
               </div>
               <div class='form-group'>
                   <label for='description'>Description</label>
                   <textarea id='description' name='description'><?php echo $homeworkInfo['description']; ?></textarea><br>
               </div>
               <div class='form-group'>
                   <label for='deadline'>Deadline:</label>
                   <input type='datetime-local' id='deadline' name='deadline' placeholder="<?php echo $homeworkInfo['deadline']; ?>"> <br>
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