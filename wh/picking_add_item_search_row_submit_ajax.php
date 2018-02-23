<?php
include 'session.php';	
include 'inc_helper.php';	
	
try{
	$s_userId = $_SESSION['userId']; 

	$pickNo = $_POST['pickNo'];
    $prodId = $_POST['prodId'];	
	$issueDate = $_POST['issueDate'];	
	$grade = $_POST['grade'];	
	$pickQty = $_POST['pickQty'];	
	
	//$issueDate = to_mysql_date($issueDate);
	
	$pdo->beginTransaction();

	$sql = "SELECT * FROM picking_detail WHERE  pickNo=:pickNo AND prodId=:prodId AND issueDate=:issueDate AND grade=:grade ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':pickNo', $pickNo);	
	$stmt->bindParam(':prodId', $prodId);	
	$stmt->bindParam(':issueDate', $issueDate);	
	$stmt->bindParam(':grade', $grade);	
	$stmt->execute();
	if($stmt->rowCount() >=1){
		//update		
		$sql = "UPDATE `picking_detail` SET qty=qty+:pickQty
		WHERE pickNo=:pickNo 
		AND prodId=:prodId
		AND issueDate=:issueDate 
		AND grade=:grade ";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':pickNo', $pickNo);	
		$stmt->bindParam(':prodId', $prodId);	
		$stmt->bindParam(':issueDate', $issueDate);	
		$stmt->bindParam(':grade', $grade);	
		$stmt->bindParam(':pickQty', $pickQty);	
		$stmt->execute();
	}else{
		//insert		
		$sql = "INSERT INTO `picking_detail` 
		(`pickNo`, `prodId`, `issueDate`, `grade`, `qty`) 
		VALUES
		(:pickNo, :prodId,:issueDate,:grade,:pickQty)";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':pickNo', $pickNo);	
		$stmt->bindParam(':prodId', $prodId);	
		$stmt->bindParam(':issueDate', $issueDate);	
		$stmt->bindParam(':grade', $grade);	
		$stmt->bindParam(':pickQty', $pickQty);	
		$stmt->execute();
	}

			
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'pickNo' => $pickNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Insert. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors.$t));
}


