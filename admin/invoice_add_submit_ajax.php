<?php
include 'inc_helper.php';  
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode
	
try{   	
	$invNo = $_POST['invNo'];
	$totalExcVat = $_POST['totalExcVat'];
	$vatAmount = $_POST['vatAmount'];
	$totalIncVat = $_POST['totalIncVat'];
	
	//$qty = $_POST['qty'];
	//$pdo->beginTransaction();
	
	$sql = "UPDATE `invoice_header` SET statusCode='B'
	, totalExcVat=:totalExcVat
	, vatAmount=:vatAmount, totalIncVat=:totalIncVat
	WHERE 1
	AND invNo=:invNo 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':totalExcVat', $totalExcVat);	
	$stmt->bindParam(':vatAmount', $vatAmount);	
	$stmt->bindParam(':totalIncVat', $totalIncVat);		
	$stmt->bindParam(':invNo', $invNo);
	$stmt->execute();	
	
	//Commit
	//$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Submit Complete.', 'invNo' => $invNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    //$pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Submit. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
