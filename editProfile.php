<?php
// Init session
session_start();

// Start interact with database
require_once 'db_config.php';

$fullname = $email = $phoneNumber = '';
$fullname_err = $email_err = $phoneNumber_err = '';

// Get current value
$sql_query = "SELECT fullname, email, phoneNumber FROM account where username = ?";
if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username = $_SESSION["username"];
   
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $fullname, $email, $phoneNumber);
        
        if (mysqli_stmt_fetch($stmt)) {
           
        }
    }
    else {
        echo "Cannot execute SQL query";
    }
    mysqli_stmt_close($stmt);
}

// Return '' if fullname is ok
function check_fullname($fullname) {
    return '';
}

function check_email($email) {
    return '';
}

function check_phoneNumber($phoneNumber) {
    return '';
}

// Post new value
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname_err = check_fullname($_POST['fullname']);
    $email_err = check_email($_POST["email"]);
    $phoneNumber_err = check_phoneNumber($_POST["phoneNumber"]);
    
    
    if (empty($fullname_err) && empty($email_err) && empty($phoneNumber_err)) {
        $sql_query = "UPDATE account SET fullname = ?, email = ?, phoneNumber = ? where username = ?";
        if ($stmt = mysqli_prepare($db_connection, $sql_query)) {
            mysqli_stmt_bind_param($stmt, "ssss", $_POST["fullname"], $_POST["email"], $_POST["phoneNumber"], $_SESSION["username"]);
            
            $update_success = false;
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                $update_success = true;
            }
            else {
                echo "Cannot execute SQL query";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($db_connection);
// End interact with database

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit profile</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>

    <div class="container">
        <h2>Edit profile</h2>
        <form action="editProfile.php" method="post">
            <div class="form-group">
                <label>Full name: </label>
                <input class="form-control" type="text" name="fullname" value="<?php echo $fullname;?>">
                <span class="help-block"><?php echo $fullname_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email: </label>
                <input class="form-control" type="email" name="email" value="<?php echo $email;?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone number: </label>
                <input class="form-control" type="tel" name="phoneNumber" value="<?php echo $phoneNumber;?>" pattern="[0-9]{7,10}">
                <span class="help-block"><?php echo $phoneNumber_err; ?></span>
            </div>
            <button class="btn btn-default">Confirm</button>
        </form>
         <?php
            if (isset($update_success) && $update_success) {
                echo '<h2>Update success</h2>';
            }
         ?>
    </div>

</body>
</html>