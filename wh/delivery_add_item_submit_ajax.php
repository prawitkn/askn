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
	
	$sql = "INSERT INTO  `delivery_detail` (prodCode, qty, createTime, doNo)
	SELECT ddi.prodCode, SUM(itm.qty), now(), :doNo 
	FROM delivery_detail_item ddi on ddi.doNo=:doNo 
	INNER JOIN product_item itm on itm.prodItemId=ddi.prodItemId 
	GROUP BY ddi.prodCode 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);		
	$stmt->execute();	
	
	$sql = "UPDATE TABLE delivery_detail_item ddi 
			INNER JOIN delivery_detail dd on dd.prodCode=ddi.prodCode 
			SET ddi.doDtlId=dd.id 
			WHERE ddi.doNo=:doNo 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);		
	$stmt->execute();	
	
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


