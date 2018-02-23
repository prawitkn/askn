<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
		
    $sentDate = $_POST['sentDate'];
	$id = $_POST['id'];
	
	$sentDate = to_mysql_date($sentDate);
	
	$sql = "
			UPDATE `order_header` SET `sentDate`=:sentDate
			, `sentById`=:s_userID
			WHERE id=:id 
			AND statusCode='P' 
			";
 
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sentDate', $sentDate);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->bindParam(':id', $id);
	$stmt->execute();
		
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Update Complete.'));   
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
