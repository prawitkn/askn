<?php

    include 'session.php';	
	
	$userId = $_POST['userId'];
    $newPassword = trim($_POST['newPassword']);
	$confirmPassword = trim($_POST['confirmPassword']);
    
	if($newPassword<>$confirmPassword){
		header('Content-Type: application/json');
		$errors = "New password and confirm password not match";
		echo json_encode(array('success' => false, 'message' => $errors));  
		exit; 
	}

	$salt = "asdadasgfd";
	$hash_userPassword = hash_hmac('sha256', $confirmPassword, $salt);
	$sql = "UPDATE wh_user SET userPassword=:hash_userPassword WHERE userId=:userId ";		
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':userId', $userId);
	$stmt->bindParam(':hash_userPassword', $hash_userPassword);		
	if ($stmt->execute()) {
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
	} else {
		header('Content-Type: application/json');
		$errors = "Error on Data Update. Please try new username. " . $pdo->errorInfo();
		echo json_encode(array('success' => false, 'message' => $errors));
	}
	
?>