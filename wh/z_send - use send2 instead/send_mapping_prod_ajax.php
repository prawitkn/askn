<?php

include 'session.php';

$sdNo = $_POST['sdNo'];

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	

	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM send WHERE sdNo=:sdNo AND statusCode<>'P' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $sdNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 1: UPDATE DATA
	$sql = "UPDATE product_item itm
	INNER JOIN product_mapping pm on itm.prodId=pm.invProdId
	SET itm.prodCodeId=pm.wmsProdId 
	WHERE itm.prodItemId IN (SELECT dtl.prodItemId FROM send_detail dtl WHERE dtl.sdNo=:sdNo) 
		";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $sdNo);
    $stmt->execute();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data mapping completed.', 'sdNo' => $sdNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data mapping. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

