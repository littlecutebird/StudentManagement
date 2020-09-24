<?php

// Return empty if upload success, else return error
$upload_err = '';

// Upload file homework
if (isset($_POST['submit'])) {
    $target_dir = "uploads/";
    $fileName = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_FILENAME);
    $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
    $increment = '';
    
    // Rename if file exist. For example file.txt -> file1.txt -> file2.txt -> ...
    while(file_exists($target_dir. $fileName . $increment . '.' . $fileType)) {
        $increment++;
    }   
    $target_file = $target_dir. $fileName . $increment . '.' . $fileType;

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 50000000000) {
        $upload_err = "Sorry, your file is too large.";
    }
    else
    // Allow certain file formats
    if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
    && $fileType != "gif" && $fileType != "txt" && $fileType != 'pdf') {
        $upload_err = "Sorry, only JPG, JPEG, PNG & GIF & txt & PDF files are allowed.";
    }

    // Check if $uploadOk is set to 0 by an error
    if (empty($upload_err)) move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file); 
}

// Upload file challenge (txt)
if (isset($_POST['submitChallenge'])) {
    $target_dir = "challenge/";
    $fileName = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_FILENAME);
    $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
    $target_file = $target_dir. $fileName . '.' . $fileType;
    
    // Rename if file exist. For example file.txt -> file1.txt -> file2.txt -> ...
    if (file_exists($target_file)) {
        $upload_err = "Challenge exists! Maybe you should rename the file?";
    }   
    else
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 50000000000) {
        $upload_err = "Sorry, your file is too large.";
    }
    else
    // Allow certain file formats
    if($fileType != "txt") {
        $upload_err = "Sorry, only txt files are allowed for challenge.";
    }

    // Check if $uploadOk is set to 0 by an error
    if (empty($upload_err)) move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file); 
}
  
?>
