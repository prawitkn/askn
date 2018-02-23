<?php
include 'session.php';

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	
	//$session_userID=$_SESSION['userID'];
	
	$sdNo = $_POST['sdNo'];

	//We start our transaction.
	$pdo->beginTransaction();	
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM send WHERE sdNo=:sdNo AND statusCode='B' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $sdNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 1: Check is settle all product Item 	
	$sql = "SELECT dtl.id FROM send_detail dtl INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId AND (itm.prodCodeId IS NULL OR itm.prodCodeId='') WHERE sdNo=:sdNo ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $sdNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 0){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Some item is not settle product code yet.'));
		exit();
	}
	
	//Query 2: UPDATE DATA
	$sql = "UPDATE send SET statusCode='C'   
		, confirmTime=now()
		, confirmById=?
		WHERE sdNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(	
			$s_userId,
			$sdNo	
        )
    );
	    
    //We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data Confirmed'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Confirmation. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

