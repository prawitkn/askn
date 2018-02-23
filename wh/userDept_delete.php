<?php

include 'session.php';	

try{
	$id = $_GET['id'];
	
	$pdo->beginTransaction();
	
	$sql = "DELETE FROM wh_user_dept WHERE id=:id ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	$pdo->commit();	
	
	header("Location: userDept.php");
}catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}

   

