<?php

include 'session.php';

try{
	$id = $_POST['id'];
	
	$pdo->beginTransaction();
	
	//delete image
	$sql = "SELECT photo FROM product WHERE id=:id ";
	$result_img = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$row = $stmt->fetch();
	
	if($row['photo']<>""){
		@unlink('./dist/img/'.$row['photo']);
	}

	$sql = "DELETE FROM product WHERE id=:id ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	$pdo->commit();	
	
	//header("Location: product.php");
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data deleted'));
	
}catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}



?>     

