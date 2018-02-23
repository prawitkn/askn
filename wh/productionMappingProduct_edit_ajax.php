<?php

    include 'session.php';	
	
    $wmsProdId = $_POST['wmsProdId'];
	$statusCode = $_POST['statusCode'];
    $id = $_POST['id'];
    
 // Check user name duplication?
    $sql = "SELECT statusCode FROM product_mapping WHERE invProdId=:id ";
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':id', $id);	
	$stmt->execute();	
	
    if ($stmt->rowCount() < 1){
      header('Content-Type: application/json');
      $errors = "Error on Data Update. Data not found. " . $pdo->errorInfo();
      echo json_encode(array('success' => false, 'message' => $errors));  
      exit;    
    } 
	
	$sql = "UPDATE `product_mapping` SET `wmsProdId`=:wmsProdId 
	, `statusCode`=:statusCode
	WHERE invProdId=:id 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':wmsProdId', $wmsProdId);
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
