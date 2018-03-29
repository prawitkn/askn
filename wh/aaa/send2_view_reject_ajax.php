<?php

include 'session.php';

$rtNo = $_POST['rtNo'];

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	

	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM rt WHERE rtNo=:rtNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rtNo', $rtNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 1: UPDATE DATA
	$sql = "UPDATE rt SET statusCode='B'
			WHERE rtNo=:rtNo
			AND statusCode='C' 
		";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rtNo', $rtNo);
    $stmt->execute();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data rejected'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on data rejection. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

