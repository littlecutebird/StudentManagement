<!DOCTYPE html>
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

// Query information about this challengeId
$challenge_sql_query = "SELECT id, title, description, filePath, modified_time, deadline FROM challenge where id = ?";
if ($stmt = mysqli_prepare($db_connection, $challenge_sql_query)) {
        // Bind variables to prepared SQL statement
      mysqli_stmt_bind_param($stmt, "i", $_GET["challengeId"]);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            $challenge_sql_result = $stmt->get_result();
            if (!($row = $challenge_sql_result ->fetch_assoc())) {
                exit("Challenge don't exist!");
            }
        }
        else {
            exit("Cannot execute SQL query");
        }
    mysqli_stmt_close($stmt);
}

// If student submit right answer, redirect to file location
$wrong_answer = '';
if (isset($_POST['submitChallenge'])) {
    $answer = pathinfo($row['filePath'], PATHINFO_FILENAME);
    if ($answer == $_POST['answer']) {
        //right answer
        header("location: {$row['filePath']}");
    }
    else {
        //wrong answer
        $wrong_answer = 'Wrong answer';
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
                <li><a href="listExercise.php"><?php if ($_SESSION["type"] == "teacher") echo "Add homework"; else echo "Homework" ?></a></li>
                <li class='active'><a href="listChallenge.php"><?php if ($_SESSION["type"] == "teacher") echo "Add challenge"; else echo "Challenge" ?></a></li>
                <li><a href="#">Page 3</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
        <div class='page-header'>
            <h1>Submit answer</h1>
        </div>
        <div class='container'> 
            <h2><?= $row['title'] ?></h2>
            <p><?="Hint: {$row['description']}" ?></p>
            <p>Deadline: <?= $row['deadline'] ?></p>
            <form action='' method='post'>
                <div class="form-group">
                    <label for="answer">Answer: </label>
                    <input class='form-control' type="text" name="answer" id="answer">
                    <?php echo $wrong_answer; ?>
                </div>
                <button class='btn btn-danger' type="submit" name="submitChallenge">Submit answer</button>
                <a class='btn btn-primary' href='listChallenge.php'>Back</a>
            </form>     
        </div>
       
    
    </body>
</html>