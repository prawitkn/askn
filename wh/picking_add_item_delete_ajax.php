<?php
include 'session.php';	
	
try{
		
    $id = $_POST['id'];
	
	$sql = "DELETE FROM picking_detail WHERE id=:id 
			";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Delete Complete.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


