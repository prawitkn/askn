<?php

include 'session.php';

$rcNo = $_POST['rcNo'];
$recvProdId = $_POST['recvProdId'];
$shelfId = $_POST['shelfId'];


//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM receive WHERE rcNo=:rcNo AND statusCode='P' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 1: Check Status for is  return item.
	$sql = "SELECT * FROM receive_detail WHERE statusCode='R' AND id IN (:recvProdId) LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':recvProdId', $recvProdId);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count == 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Cannot set shelf for return item.'));
		exit();
	}
	
	
	//Query 1: Delete old wh sloc.
	$sql = "DELETE FROM wh_shelf_map_item WHERE recvProdId IN (:recvProdId)";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':recvProdId', $recvProdId);
    $stmt->execute();
	
	//Query 1: Delete old wh sloc.
	$sql = "INSERT INTO wh_shelf_map_item (shelfId, recvProdId, statusCode) 
	SELECT :shelfId, id, 'A' FROM receive_detail WHERE id IN (:recvProdId) ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':shelfId', $shelfId);
	$stmt->bindParam(':recvProdId', $recvProdId);
    $stmt->execute();
	
	//Query 1: Delete old wh sloc.
	/*$sql = "UPDATE receive_detail set shelfCode=:slocCode WHERE id=:recvProdId ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':slocCode', $slocCode);
	$stmt->bindParam(':recvProdId', $recvProdId);
    $stmt->execute();*/
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Set Shelf Completed.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on shelf setting. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

