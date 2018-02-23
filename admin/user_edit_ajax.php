<?php

    include 'session.php';	
	
	$userId = $_POST['userId'];
    $userFullname = $_POST['userFullname'];
    $userName = $_POST['userName'];
    $userPassword = $_POST['userPassword'];
    $userEmail = $_POST['userEmail'];
    $userTel = $_POST['userTel'];
	$userGroupCode = $_POST['userGroupCode'];
	$smId = $_POST['smId'];
	$statusCode = $_POST['statusCode'];
    
	$curPhoto = $_POST['curPhoto'];
	$new_picture_name=$curPhoto;
	
	
	
 // Check user name duplication?
    $sql = "SELECT userName,userPassword, userPicture FROM user WHERE userId=$userId ";
    //$result_user = mysqli_query($link, $sql_user);
    //$is_user = mysqli_num_rows($result_user);
	
	$stmt = $pdo->prepare($sql);	
	$stmt->execute();	
	//$result = $stmt->rowCount();
	

    if ($stmt->rowCount() <> 1){
      header('Content-Type: application/json');
      $errors = "Error on Data Update. Please try new username. " . $pdo->errorInfo();
      echo json_encode(array('success' => false, 'message' => $errors));  
      exit;    
    }   
    $row=$stmt->fetch();
	
	$hash_userPassword='';
	if(isset($userPassword) AND $userPassword<>''){
		 // Encript New Password
		$salt = "asdadasgfd";
		$hash_userPassword = hash_hmac('sha256', $userPassword, $salt);
	}else{
		//Old Password
		$hash_userPassword=$row['userPassword'];
	}
	
  
	//inputFile
	if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
		// If the old picture already exists, delete it
		if (file_exists('dist/img/'.$curPhoto)) unlink('dist/img/'.$curPhoto);
	
        $new_picture_name = 'user_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
        $path_upload = "dist/img/".$new_picture_name;
        move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
    }
	
    
	$sql = "UPDATE `user` SET `userName`=:userName 
	, `userPassword`=:userPassword
	, `userFullname`=:userFullname
	, `userEmail`=:userEmail 
	, `userTel`=:userTel
	, `userPicture`=:new_picture_name
	, `userGroupCode`=:userGroupCode
	, `smId`=:smId
	, `statusCode`=:statusCode 
	WHERE userId=:userId 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':userName', $userName);
	$stmt->bindParam(':userPassword', $hash_userPassword);
	$stmt->bindParam(':userFullname', $userFullname);
	$stmt->bindParam(':userEmail', $userEmail);
	$stmt->bindParam(':userTel', $userTel);
	$stmt->bindParam(':new_picture_name', $new_picture_name);
	$stmt->bindParam(':userGroupCode', $userGroupCode);
	$stmt->bindParam(':smId', $smId);
	$stmt->bindParam(':statusCode', $statusCode);
	$stmt->bindParam(':userId', $userId);
	;	
 
    if ($stmt->execute()) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Update. Please try new username. " . $pdo->errorInfo();
      echo json_encode(array('success' => false, 'message' => $errors));
}
