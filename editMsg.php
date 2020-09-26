<?php
// Init session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
// Start interact with database
require_once 'db_config.php';

// Store old message, will edit later
$oldMessageContent = '';

// Find old content
$sql_query = 'SELECT content FROM message WHERE id = ?';
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    mysqli_stmt_bind_param($stmt, "s", $_GET['messageId']);
    if (mysqli_stmt_execute($stmt)) {
        $oldMessageContent = $stmt ->get_result() -> fetch_assoc()['content'];
    }
    else {
        exit("Cannot execute select old contentMsg SQL query");
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['messageContent']) && !empty($_POST['messageContent'])) {
    // Edit msg in database
    $sql_query = 'UPDATE message SET content = ? WHERE id = ?';
    if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
        mysqli_stmt_bind_param($stmt, "si", $_POST['messageContent'], $_GET['messageId']);
        if (mysqli_stmt_execute($stmt)) {
            // Update message ok, redirect to sendMsg page
            header("Location: sendMsg.php?userId={$_GET['userId']}#bottomPage"); 
        }
        else {
            exit("Cannot execute updateMsg SQL query");
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($db_connection);
// End interact with database

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit message</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles/mycss.css">
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
                <li class="active"><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
     <div class="page-header">
        <h1>Edit message</h1>
    </div>
    <div class="container">
        <form action='' method='post'>
            <div class="input-group">
                <input type="text" style=' background: linear-gradient(to left, #ffefba, #ffffff);font-size:15px;font-weight:550;color: black;' class="form-control " placeholder="Send new messeage" name='messageContent' value='<?= $oldMessageContent?>'>
                <span class="input-group-btn">
                    <button style="background: linear-gradient(to right, #f12711, #f5af19);" type="submit" class="btn btn-default">
                        <span class="glyphicon glyphicon-send"></span>
                    </button>
                </span>
             </div>  
        </form>    
    </div>

</body>
</html>