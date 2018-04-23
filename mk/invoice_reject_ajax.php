<?php	
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode

try{
    $invNo = $_POST['invNo'];	
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT invNo FROM invoice_header WHERE invNo=:invNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':invNo', $invNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}	
		
	//Query 1: UPDATE DATA
	$sql = "UPDATE `invoice_header` SET statusCode='B'
			WHERE invNo=:invNo";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':invNo', $invNo);	
	$stmt->execute();
		
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data rejected', 'invNo' => $invNo));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Reject. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


