<?php
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID']; 
	
    $doNo = $_POST['doNo'];
	$rowCount =  count($_POST['prodCode']); 
	
	//$qty = $_POST['qty'];
	$pdo->beginTransaction();
	
	$sql = "DELETE FROM `delivery_detail` WHERE doNo=:doNo";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);		
	$stmt->execute();	
	
    for($i=1; $i<=$rowCount; $i++)
	{
		$sql = "INSERT INTO  `delivery_detail` (prodCode, qty, createTime, doNo) VALUES
		(:prodCode, :qty, now(), :doNo)";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':prodCode', $_POST['prodCode'][$i -1]);	
		$stmt->bindParam(':qty', $_POST['qty'][$i -1]);	
		$stmt->bindParam(':doNo', $doNo);		
		$stmt->execute();	
	}
			
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'doNo' => $doNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


