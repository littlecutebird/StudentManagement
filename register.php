<?php
session_start();

// Only admin or teacher can register, admin can register teacher and student but teacher can only register student
if (!isset($_SESSION["type"]) || ($_SESSION["type"] == "student")) {
    http_response_code(404);
    exit;
}

// Include config file
require_once "db_config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $fullname= $email = $phoneNumber = $type = "";
$username_err = $password_err = $confirm_password_err = $fullname_err = $email_err = $phoneNumber_err = "";
$reg_ok = '';

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM account WHERE username = ?";
        
        if($stmt = mysqli_prepare($db_connection, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Cannot execute SQL query.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Validate full name
    if(empty(trim($_POST["fullname"]))){
        $fullname_err = "Please enter fullname";
    }
    else {
        $fullname = trim($_POST["fullname"]);
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email";
    }
    else {
        $email = trim($_POST["email"]);
    }
    
    // Validate phone number
    if(empty(trim($_POST["phoneNumber"]))){
        $phoneNumber_err = "Please enter phone number";
    }
    else {
        $phoneNumber = trim($_POST["phoneNumber"]);
    }
    
    // Set type
    if (isset($_POST["type"])) $type = $_POST["type"]; // admin account
    else $type = "student"; // teacher account
        
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($fullname_err) && empty($email_err) && empty($phoneNumber_err)){
        
        // Prepare an insert statement
        $sql_query = "INSERT INTO account (username, password, type, fullname, email, phoneNumber) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($db_connection, $sql_query)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_type, $param_fullname, $param_email, $param_phoneNumber);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_type = $type;
            $param_fullname = $fullname;
            $param_email = $email;
            $param_phoneNumber = $phoneNumber;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $reg_ok = 'Create account success! Please contact user to log in to this account.';
            } else{
                exit("Cannot execute insert SQL query");
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($db_connection);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="styles/mycss.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
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
                <li class="active"><a href="listUser.php">List user</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="profile.php?username=<?php echo $_SESSION['username']?>"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <div class="center">
            <h2>Sign up</h2>
            <p>Please fill this form to create an account.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                    <span class="help-block"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($fullname_err)) ? 'has-error' : ''; ?>">
                    <label>Full name</label>
                    <input type="text" name="fullname" class="form-control" value="<?php echo $fullname; ?>">
                    <span class="help-block"><?php echo $fullname_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $email_err; ?></span>
                </div>
                <div class="form-group <?php echo (!empty($phoneNumber_err)) ? 'has-error' : ''; ?>">
                    <label>Phone number</label>
                    <input type="tel" name="phoneNumber" class="form-control" value="<?php echo $phoneNumber; ?>">
                    <span class="help-block"><?php echo $phoneNumber_err; ?></span>
                </div>
                <?php
                if ($_SESSION["username"] == "admin") {
                    echo '<div class="form-group">
                             <label>Account type:</label>
                             <select name="type">
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                             </select>
                          </div>';
                }
                ?>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-default" value="Reset">
                </div>
                <p><?php echo $reg_ok?></p>
            </form>
        </div>   
    </div>
</body>
</html>