<?php
include 'session.php';	
include '../db/database_sqlsrv.php';
include 'inc_helper.php';	
	
try{
	$t = "";
	$s_userID = $_SESSION['userID']; 
	
    $sdNo = $_POST['sdNo'];
	/*$barcode = $_POST['barcode'];
	$prodCode = $_POST['prodCode'];
	$qty = $_POST['qty'];
	$remark = $_POST['remark'];*/
	
	$pdo->beginTransaction(); 
	
	
	if(!empty($_POST['prodItemId']) and isset($_POST['prodItemId']))
    {
		//$arrProdItems=explode(',', $prodItems);
        foreach($_POST['prodItemId'] as $item)
        {				
			$sql = "INSERT INTO  `send_detail` 
			(`prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`
			, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`
			, `remark`, `problemId`, `sdNo`) 
			SELECT `prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`
			, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`
			, `remark`, `problemId`, :sdNo 
			FROM product_item WHERE prodItemId=:prodItemId ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':prodItemId', $item);
			$stmt->bindParam(':sdNo', $sdNo);		
			$stmt->execute();
        }
    }
		
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'sdNo' => $sdNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors.$t));
}


