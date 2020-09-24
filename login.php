<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
// Init session
session_start();

//Check if user already log in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    header("location: index.php");
    exit;
}

//include db config file
require_once "db_config.php";

//Init variable with empty value
$username = $password = '';
$username_err = $password_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username";
    }
    else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password";
    }
    else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare SQL statement
        $sql_query = "SELECT id, username, password, type FROM account where username = ?";
        
        if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
            // Bind variables to prepared SQL statement
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set param
            $param_username = $username;
            
            // Execute SQL statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                // If username exist, verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $type);
                    
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            //password is correct
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["username"] = $username;
                            $_SESSION["type"] = $type;
                            $_SESSION["id"] = $id;
                            
                            header("location: index.php");
                        }
                        else {
                            $password_err = "The password you entered is not valid";
                        }
                    }
                }
                else {
                    $username_err = "Username not exists";
                }
            }
            else {
                echo "Please try again, cannot execute SQL query";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($db_connection);
}   
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <title>Log in</title>
        <link rel="stylesheet" href="styles/mycss.css">
        <style>
            .center {
                margin: 0;
                position: absolute;
                top: 50%;
                left: 50%;
                -ms-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
            }
        </style>
    </head>
    <body>
        <div class="center">
            <h2>Login</h2>
            <p><i>Please fill in your credentials to login.</i></p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                
            </form>
        </div>    
    </body>
</html>
