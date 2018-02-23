<?php

    include 'session.php';	
	
   
    $userFullname = $_POST['userFullname'];
    $userName = $_POST['userName'];
    $userPassword = mysqli_real_escape_string($link, $_POST['userPassword']);
    $userEmail = $_POST['userEmail'];
    $userTel = $_POST['userTel'];
	$userGroupCode = $_POST['userGroupCode'];
	$userDeptCode = $_POST['userDeptCode'];
    
 // Check user name duplication?
    $sql_user = "SELECT userName FROM wh_user WHERE userName='$userName'";
    $result_user = mysqli_query($link, $sql_user);
    $is_user = mysqli_num_rows($result_user);
    if ($is_user >= 1){
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try new username. " . mysqli_error($link);
      echo json_encode(array('success' => false, 'message' => $errors));  
      exit;    
    }   
    
 // Encript Password
    $salt = "asdadasgfd";
    $hash_userPassword = hash_hmac('sha256', $userPassword, $salt);

	$new_picture_name="";
	 // Upload Picture
    if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
        $new_picture_name = 'user_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
        $path_upload = "dist/img/".$new_picture_name;
        move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
    }
	
    
    $sql = "INSERT INTO `wh_user` (`userName`, `userPassword`, `userFullname`, `userEmail`, `userTel`, `userPicture`, `userGroupCode`,  `userDeptCode`, `statusCode`)"
            . " VALUES ('$userName', '$hash_userPassword', '$userFullname', '$userEmail', '$userTel', '$new_picture_name', '$userGroupCode', '$userDeptCode','A')";
 
    $result = mysqli_query($link, $sql);
 
    if ($result) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try new username. " . mysqli_error($link);
      echo json_encode(array('success' => false, 'message' => $errors));
}
