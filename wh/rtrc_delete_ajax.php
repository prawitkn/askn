<?php	
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode

try{
    $rcNo = $_POST['rcNo'];	
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT rcNo FROM receive WHERE rcNo=:rcNo AND statusCode<>'P' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);
	$stmt->execute();
	$hdr = $stmt->fetch();	
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}	
		
	//Query 1: DELETE Detail
	$sql = "DELETE FROM `receive_detail` WHERE rcNo=:rcNo";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);	
	$stmt->execute();
	
	//Query 2: DELETE Header
	$sql = "DELETE FROM `receive` WHERE rcNo=:rcNo";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);	
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
	$errors = "Error on data deleting. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


