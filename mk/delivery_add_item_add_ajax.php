<?php
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID']; 
	
    $doNo = $_POST['doNo'];
	$prodCode = $_POST['prodCode'];
	$qty = $_POST['qty'];
	
	$pdo->beginTransaction();
	
	$sql = "SELECT id FROM delivery_detail dd 
			WHERE 1
			AND dd.doNo=:doNo AND dd.prodCode=:prodCode LIMIT 1
			";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->bindParam(':prodCode', $prodCode);
	$stmt->execute();
	
	if($stmt->rowCount() > 0){
		$sql = "UPDATE `delivery_detail` dd SET dd.qty=:qty WHERE dd.doNo=:doNo AND dd.prodCode=:prodCode ";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':qty', $qty);	
		$stmt->bindParam(':doNo', $doNo);	
		$stmt->bindParam(':prodCode', $prodCode);	
		$stmt->execute();
	}else{
		$sql = "INSERT INTO  `delivery_detail` (prodCode, qty, createTime, doNo) VALUES
		(:prodCode, :qty, now(), :doNo)";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':prodCode', $prodCode);	
		$stmt->bindParam(':qty', $qty);	
		$stmt->bindParam(':doNo', $doNo);		
		$stmt->execute();
	}		
	
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


