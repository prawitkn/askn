<?php
include 'session.php';	
	
try{
	
	$s_userId = $_SESSION['userId']; 	
    $id = $_POST['id'];
	
	$pdo->beginTransaction();
	
	$sql = "DELETE FROM prepare_detail WHERE id=:id 
			";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	
	//Delete scanned
	$sql = "DELETE FROM prepare_scan WHERE refId=:id 
			";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Delete Complete.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


