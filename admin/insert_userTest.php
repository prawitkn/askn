<?php

    include '../db/database.php';
   
    $userFullname = $_POST['userFullname'];
    $userName = $_POST['userName'];
    $userPassword = $_POST['userPassword'];
    $userEmail = $_POST['userEmail'];
    $userTel = $_POST['userTel'];
   
 // Encript Password
    $salt = "asdadasgfd";
    $hash_userPassword = hash_hmac('sha256', $userPassword, $salt);
    
 // Upload Personal Picture
    if (is_uploaded_file($_FILES['userPicture']['tmp_name'])){
        $new_picture_name = 'user_'.uniqid().".".pathinfo(basename($_FILES['userPicture']['name']), PATHINFO_EXTENSION);
        $path_upload = "./dist/img/".$new_picture_name;
        move_uploaded_file($_FILES['userPicture']['tmp_name'], $path_upload);
        
    }  else {
        $new_picture_name = "";
       
    }
    
    $sql = "INSERT INTO `user` (`userName`, `userPassword`, `userFullname`, `userEmail`, `userTel`, `userPicture`)"
            . " VALUES ('$userName', '$hash_userPassword', '$userFullname', '$userEmail', '$userTel', '$new_picture_name')";
 
    $result = mysqli_query($link, $sql);
 
    if ($result) {
 //     header("Location: product_type.php");
 //     echo "Finished Insert.";
      header('Content-Type: application/json');
      echo json_encode(array('status' => 'success', 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try new username. " . mysqli_error($link);
      echo json_encode(array('status' => 'danger', 'message' => $errors));
 //   echo " Cannot Insert.";
 //   echo mysqli_error($link);
}
