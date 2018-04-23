<?php

include 'session.php';	

try{
	$id = $_GET['id'];
	
	$pdo->beginTransaction();
	
	//delete image
	$sql = "SELECT userPicture FROM user WHERE userId=:id ";
	$result_img = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	$row = $stmt->fetch();
	
	@unlink('./dist/img/'.$row['userPicture']);

	$sql = "DELETE FROM user WHERE userId=:id ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	$pdo->commit();	
	
	header("Location: user.php");
}catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}

   

