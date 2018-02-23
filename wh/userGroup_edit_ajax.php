<?php

    include 'session.php';	
	
	$code = $_POST['code'];
    $name = $_POST['name'];
	$statusCode = $_POST['statusCode'];
    $id = $_POST['id'];
    
 // Check user name duplication?
    $sql = "SELECT statusCode FROM wh_user_group WHERE id=:id ";
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':id', $id);	
	$stmt->execute();	
	
    if ($stmt->rowCount() < 1){
      header('Content-Type: application/json');
      $errors = "Error on Data Update. Data not found. " . $pdo->errorInfo();
      echo json_encode(array('success' => false, 'message' => $errors));  
      exit;    
    } 
	
	$sql = "UPDATE `wh_user_group` SET `code`=:code 
	, `name`=:name
	, `statusCode`=:statusCode
	WHERE id=:id 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':code', $code);
	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':statusCode', $statusCode);
	$stmt->bindParam(':id', $id);	
 
    if ($stmt->execute()) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Update. Please try again. " . $pdo->errorInfo();
      echo json_encode(array('success' => false, 'message' => $errors));
}
