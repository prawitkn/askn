<?php

include 'session.php';

try{
	$id = $_POST['id'];

	//SQL 
	$sql = "DELETE FROM visit_customer
			WHERE id=:id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	//Return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data deleted'));
}catch(Exception $e){
	//Return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}		

?>     

