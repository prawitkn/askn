<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
			
	$soNo = $_POST['soNo'];
	
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT soNo, deliveryRemImg FROM sale_header WHERE soNo=:soNo AND statusCode<>'P' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);
	$stmt->execute();
	$hdr = $stmt->fetch();	
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}	
	
	//DELETE Image
	if($hdr['deliveryRemImg']<>""){
		@unlink('./dist/img/soDeli/'.$hdr['deliveryRemImg']);
	}
	
	//Update data
	$sql = " 
	UPDATE `sale_header` SET `deliveryRemImg`="" 
	WHERE soNo=:soNo 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);
	$stmt->execute();		
		
	//Commit
	$pdo->commit();
	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Delete File Completed.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Update. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
