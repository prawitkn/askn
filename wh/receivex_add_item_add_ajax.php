<?php
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID']; 
	
    $rcNo = $_POST['rcNo'];
	$barcode = $_POST['barcode'];
	$prodCode = $_POST['prodCode'];
	$qty = $_POST['qty'];
	$xyz = $_POST['xyz'];
	$remark = $_POST['remark'];
	
	$pdo->beginTransaction();
	
	$sql = "INSERT INTO  `receive_detail` 
	(`prodCode`, `barcode`, `qty`, `xyz`, `remark`, `rcNo`) 
	VALUES
	(:prodCode,:barcode, :qty, :xyz, :remark, :rcNo)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':prodCode', $prodCode);	
	$stmt->bindParam(':barcode', $barcode);	
	$stmt->bindParam(':qty', $qty);	
	$stmt->bindParam(':xyz', $xyz);	
	$stmt->bindParam(':remark', $remark);	
	$stmt->bindParam(':rcNo', $rcNo);		
	$stmt->execute();
		
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'rcNo' => $rcNo));
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


