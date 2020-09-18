<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "db_config.php";

$oldPassword_err = '';
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //Validate old password
    //First, get real old password
    $sql_query = "SELECT password FROM account WHERE id = ?";
    if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
        // Bind variables to prepared SQL statement
        mysqli_stmt_bind_param($stmt, 'i', $_SESSION["id"]);

        // Execute SQL statement
        if (mysqli_stmt_execute($stmt)) {
            $res = $stmt->get_result();
            $row = $res -> fetch_assoc();
            // This is the previous hashed password of user
            $realOldPassword = $row["password"];
            // Check password user type in with this hash above
            if (!password_verify($_POST["oldPassword"], $realOldPassword)) {
                $oldPassword_err = 'Old password is not correct!!! Are you trying to hack this account???';
            }
             
        }
        else {
            echo "Cannot execute SQL select password query";
            exit;
        }
        mysqli_stmt_close($stmt);
    }
    
    // Update new password
    if ($oldPassword_err == '') {
        $newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);
        $sql_query = "UPDATE account SET password = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
            // Bind variables to prepared SQL statement
            mysqli_stmt_bind_param($stmt, 'si', $newPassword, $_SESSION["id"]);
            
            // Execute SQL statement
            if (mysqli_stmt_execute($stmt)) {
               $oldPassword_err = 'Update password success my master ...';  
            }
            else {
                echo "Cannot execute SQL select password query";
                exit;
            }
            mysqli_stmt_close($stmt);
        }
       
    }
}



mysqli_close($db_connection);
?>
<html>
    <head>
        <title></title>
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
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="listExercise.php"><?php if ($_SESSION["type"] == "teacher") echo "Add homework"; else echo "Homework" ?></a></li>
                    <li><a href="listChallenge.php"><?php if ($_SESSION["type"] == "teacher") echo "Add challenge"; else echo "Challenge" ?></a></li>
                    <li><a href="listUser.php">List user</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class='active'><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                </ul>
            </div>
        </nav>
        <div class='page-header'>
            <h1>Change password</h1>
        </div>
        <div class="container">
            <p>Please fill in your old and your new password</p>
            <form action="" method="post" >
                <div class="form-group">
                    <label for="oldPassword">Old password:</label>
                    <input class="form-control" type="password" id="oldPassword" name="oldPassword" placeholder="Enter your old password" required>
                    <small id="oldPasswordHelpText" class="form-text text-muted">Your previous password</small>
                </div>
                <div class="form-group">
                    <label for="newPassword">New password:</label>
                    <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="Enter your new password" minlength="8" maxlength="16" pattern=".*[0-9]+.*" required>
                    <small id="newPasswordHelpText" class="form-text text-muted">Your password must consist 8-16 characters and contain at least one digit</small>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm new password:</label>
                    <input class="form-control" type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your new password" minlength="8" maxlength="16" required>
                    <small id="confirmPasswordHelpText" class="form-text text-muted">You should type the same password as the new password above</small>
                </div>
                <button type="submit" class="btn btn-primary">Change password</button>
                <span class="help-block"><?php echo $oldPassword_err; ?></span>
            </form>
            <script type="text/javascript">
                var password = document.getElementById("newPassword"), confirm_password = document.getElementById("confirmPassword");

                function validatePassword(){
                  if(newPassword.value != confirmPassword.value) {
                    confirmPassword.setCustomValidity("Passwords Don't Match");
                  } else {
                    confirmPassword.setCustomValidity('');
                  }
                }

                newPassword.oninput = validatePassword;
                confirmPassword.oninput = validatePassword;
            </script>
        </div>
    </body>
</html>
