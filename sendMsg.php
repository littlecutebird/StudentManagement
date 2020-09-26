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

// Get fullname of receiver
$sql_query = 'SELECT fullname FROM account WHERE id = ?';
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    mysqli_stmt_bind_param($stmt, "i", $_GET['userId']);
    if (mysqli_stmt_execute($stmt)) {
        $receiverInfo = $stmt ->get_result() -> fetch_assoc();
    }
    else {
        exit("Cannot execute select fullname SQL query");
    }
    mysqli_stmt_close($stmt);
}

// Get all messages from receiver ($_GET['userId']) and sender ($_SESSION['id'])
$sql_query = 'SELECT * FROM message WHERE (sendId = ? AND receiveId = ?) OR (sendId = ? AND receiveId = ?) ORDER BY sendTime';
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    mysqli_stmt_bind_param($stmt, "iiii", $_SESSION['id'], $_GET['userId'], $_GET['userId'], $_SESSION['id']);
    if (mysqli_stmt_execute($stmt)) {
        $allMessage = $stmt ->get_result();
        
    }
    else {
        exit("Cannot execute select message SQL query");
    }
    mysqli_stmt_close($stmt);
}

// Insert new message to database
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newMessage']) && !empty($_POST['messageContent'])) {
    $sql_query = 'INSERT INTO message SET sendId = ?, receiveId = ?, content = ?, sendTime = now()';
    if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
        mysqli_stmt_bind_param($stmt, "iis", $_SESSION['id'], $_GET['userId'], $_POST['messageContent']);
        if (mysqli_stmt_execute($stmt)) {
            // Insert new message success
            header('Location:'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
        }
        else {
            exit("Cannot execute insert new message SQL query");
        }
        mysqli_stmt_close($stmt);
    }
}

// Close database connection
mysqli_close($db_connection);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send message</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="styles/mycss.css">
    <style>
        .well {
            margin:auto;
            font-size:15px;
            font-weight:550;
            color: #f3f3f3;
            border-bottom-left-radius: 1.3em;
            border-bottom-right-radius: 1.3em;
            border-top-left-radius: 1.3em;
            border-top-right-radius: 1.3em;
            background-color: #1fc8db;
            background-image: linear-gradient(140deg, #EADEDB 0%, #BC70A4 50%, #BFD641 75%);
        }
    </style>
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
                <li class='active'><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="page-header">
        <h1>Chat with <?= $receiverInfo['fullname']?></h1>
    </div>
    
    <div class="container">
        <?php
        while ($message = $allMessage -> fetch_assoc()) {
            // sender fullname: $_SESSION['fullname']
            // receiver fullname: $receiverInfo['fullname']
            if ($message['sendId'] == $_SESSION['id']) {
                //this message is sent by this user
                echo "
                <div class='media'>
                    <div class='media-body text-right'>
                        <h3 class='media-heading'>{$_SESSION['fullname']}</h3>
                        <div class='well'>{$message['content']}</div>
                        
                        <a href='editMsg.php?messageId={$message['id']}&userId={$_GET['userId']}'>
                            <span class='glyphicon glyphicon-pencil' style='color:white'></span>
                        </a>
                        <a onclick=\"return confirm('Are you sure you want to delete this message?')\" href='deleteMsg.php?messageId={$message['id']}'>
                            <span class='glyphicon glyphicon-minus' style='color:red'></span>
                        </a> &#160
                    </div>
                    <div class='media-right media-top'>
                        <img src='img/sendMsg-logo.png' class='media-object' style='width:80px'>
                    </div>
                </div>
                ";
            }
            else if ($message['sendId'] == $_GET['userId']){
                //this message is sent by receiver
                echo "
                <div class='media'>
                    <div class='media-left media-top'>
                        <img src='img/receiveMsg-logo.png' class='media-object' style='width:80px'>
                    </div>
                    <div class='media-body'>
                        <h3 class='media-heading'>{$receiverInfo['fullname']}</h3>
                        <div class='well'>{$message['content']}</div>           
                    </div>
                </div>
                ";
            }
            
        }
        ?>
       
        <div id="bottomPage" >
            <br>
            <form action='' method='post'>
                <div class="input-group">
                    <input type="text" style='background: linear-gradient(to left, #ffefba, #ffffff);font-size:15px;font-weight:550;color: black;' class="form-control " placeholder="Send new messeage" name='messageContent' >
                    <span class="input-group-btn">
                        <button style="background: linear-gradient(to right, #f12711, #f5af19);" type="submit" name='newMessage' class="btn btn-default">
                            <span class="glyphicon glyphicon-send"></span>
                        </button>
                    </span>
                 </div>  
            </form>     
        </div>
        <br>
       
    </div>
  
</body>
</html>