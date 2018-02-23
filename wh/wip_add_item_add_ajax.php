<?php
include 'session.php';	
	
try{
		
    $wipNo = $_POST['wipNo'];
	$barcode = $_POST['barcode'];
	
	if(!isset($_SESSION['ppData'])) { $_SESSION['ppData']= array(); }

	foreach ($_SESSION['ppData'] as $eacharray) 
	{
		if($eacharray == $barcode){
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Duplicate scan.'));
			exit();
		}
	}
	array_push($_SESSION['ppData'],$barcode);
	
	

	$pdo->beginTransaction();
		
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT id, prodItemId, barcodeId, refNo 
			FROM (SELECT id, dtl.prodItemId, REPLACE(itm.`barcode`, '-', '') as barcodeId, rcNo as refNo
				FROM receive_detail dtl
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
				WHERE dtl.statusCode='A' ) as tmp			
			WHERE barcodeId=:barcode
			LIMIT 1 ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':barcode', $barcode);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Not found.'));
		exit();
	}
	$row=$stmt->fetch();
	$prodItemId = $row['prodItemId'];
	$refNo = $row['refNo'];
	
	
	//For Re-scan 
	$sql = "SELECT id FROM wip_detail WHERE prodItemId=:prodItemId LIMIT 1";	
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':prodItemId', $prodItemId);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count == 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Duplicate scan.'));
		exit();
	}
	
	$sql = "INSERT INTO  `wip_detail` 
	(`prodItemId`, `wipNo`, `refNo`) 
	VALUES (:prodItemId, :wipNo, :refNo)
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':prodItemId', $prodItemId);
	$stmt->bindParam(':wipNo', $wipNo);
	$stmt->bindParam(':refNo', $refNo);
	$stmt->execute();
	
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
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


