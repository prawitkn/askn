<?php
include 'session.php';	
	
try{
		
    $ppNo = $_POST['ppNo'];
	$barcode = $_POST['barcode'];
	
	/*if(!isset($_SESSION['ppData'])) { $_SESSION['ppData']= array(); }

	foreach ($_SESSION['ppData'] as $eacharray) 
	{
		if($eacharray == $barcode){
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Duplicate scan.'));
			exit();
		}
	}
	array_push($_SESSION['ppData'],$barcode);
	*/
	

	$pdo->beginTransaction();
		
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT prodItemId 
			FROM (SELECT prodItemId, REPLACE(`barcode`, '-', '') as barcodeId 
					FROM product_item  
					 
					 ) as tmp			
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
	
	//For Re-scan 
	$sql = "SELECT refId FROM prepare_scan WHERE barcodeId=:barcode AND userId=:s_userId LIMIT 1";	
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':barcode', $barcode);
	$stmt->bindParam(':s_userId', $s_userId);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count == 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Duplicate scan.'));
		exit();
	}
		
	//For Re-scan 
	/*$sql = "SELECT id FROM prepare_detail WHERE prodItemId=:prodItemId AND ppNo=:ppNo LIMIT 1";	
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':prodItemId', $prodItemId);
	$stmt->bindParam(':ppNo', $ppNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count == 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Duplicate scan.'));
		exit();
	}*/
	
	$sql = "INSERT INTO  `prepare_detail` 
	(`prodItemId`, `ppNo`) 
	VALUES (:prodItemId, :ppNo) 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':prodItemId', $prodItemId);
	$stmt->bindParam(':ppNo', $ppNo);
	$stmt->execute();
	
	$tmpId = $pdo->lastInsertId(); 
	
	//Insert scanned
	$sql = "INSERT INTO  `prepare_scan` 
	(`barcodeId`,`refId`, `userId`) 
	VALUES (:barcode,:tmpId,:s_userId) 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':barcode', $barcode);
	$stmt->bindParam(':tmpId', $tmpId);
	$stmt->bindParam(':s_userId', $s_userId);
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
	$errors = "Error on data inserting. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


