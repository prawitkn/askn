<?php
include 'session.php';

try{   
    $id = $_POST['id'];

	$sql = "DELETE FROM receive_detail WHERE id=:id ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);	
	$stmt->execute();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data deleted Complete.'));
}catch(Exception $e){
	header('Content-Type: application/json');
	$errors = "Error on Data delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

