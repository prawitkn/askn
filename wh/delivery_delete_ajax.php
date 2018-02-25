<?php	
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode

try{
    $doNo = $_POST['doNo'];	
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT doNo FROM delivery_header WHERE doNo=:doNo AND statusCode<>'P' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}	
	
	//Query 1: DELETE Detail
	$sql = "DELETE FROM `delivery_prod` WHERE doNo=:doNo";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);	
	$stmt->execute();
	
	//Query 1: DELETE Detail
	$sql = "DELETE FROM `delivery_detail` WHERE doNo=:doNo";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);	
	$stmt->execute();
	
	//Query 2: DELETE Header
	$sql = "DELETE FROM `delivery_header` WHERE doNo=:doNo";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);	
	$stmt->execute();
		
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
		
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data deleted'));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback
	$pdo->rollback();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Reject. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


