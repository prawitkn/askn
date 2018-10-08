<?php

    include 'session.php';	
	
    $wmsProdId = $_POST['wmsProdId'];
	$statusCode = $_POST['statusCode'];
    $id = $_POST['id'];    
	
	try{
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
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//update mapping table 
	$sql = "UPDATE `product_mapping` SET `wmsProdId`=:wmsProdId 
	, `statusCode`=:statusCode
	WHERE invProdId=:id 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':wmsProdId', $wmsProdId);
	$stmt->bindParam(':statusCode', $statusCode);
	$stmt->bindParam(':id', $id);	
	$stmt->execute();
 	
	//Auto mapping product_item 
	$sql = "UPDATE product_item itm 
	SET itm.prodCodeId=:wmsProdId 
	WHERE itm.prodId=:id 
	AND (itm.prodCodeId = 0 OR itm.prodCodeId IS NULL) 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':wmsProdId', $wmsProdId);
	$stmt->bindParam(':id', $id);	
	$stmt->execute();
	
	//We've got this far without an exception, so commit the changes.
	$pdo->commit();
	
	//return JSON	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
	} 
	//Our catch block will handle any exceptions that are thrown.
	catch(Exception $e){
		//Rollback the transaction.
		$pdo->rollBack();
		//return JSON
		header('Content-Type: application/json');
		$errors = "Error on Data Update. " . $e->getMessage();
		echo json_encode(array('success' => false, 'message' => $errors));
	}  
