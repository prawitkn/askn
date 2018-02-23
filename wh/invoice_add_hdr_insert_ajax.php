<?php
include 'inc_helper.php';  
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode
	
try{   
	$doNo = $_POST['doNo'];
	$refNo = $_POST['refNo'];
	$invoiceDate = $_POST['invoiceDate'];
	$remark = $_POST['remark'];
	$invNo = substr(str_shuffle(MD5(microtime())), 0, 10);
	
	$invoiceDate = to_mysql_date($invoiceDate);
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	$sql = "INSERT INTO `invoice_header`
	(`invNo`, `doNo`, `refNo`, `invoiceDate`, `custCode`, `smCode`, `remark`, `statusCode`, `createTime`, `createByID`) 
	SELECT :invNo, `doNo`, :refNo, :invoiceDate, `custCode`, `smCode`, :remark, 'B', now(), :s_userID FROM `delivery_header` 
	WHERE doNo=:doNo 	
	";
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':invNo', $invNo);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->bindParam(':refNo', $refNo);
	$stmt->bindParam(':invoiceDate', $invoiceDate);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->execute();
	
	//`prodCode`, `salesPrice`, `qty`, `total`, `discPercent`, `discAmount`, `netTotal`,
	$sql = "INSERT INTO `invoice_detail`
	(`prodCode`, `salesPrice`, `qty`, `total`, `discPercent`, `discAmount`, `netTotal`, `invNo`) 
	SELECT `prodCode`, 0, `qty`, 0, 0, 0, 0, :invNo 
	FROM delivery_detail dd 
	WHERE doNo=:doNo 	
	";
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':invNo', $invNo);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->execute();
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
	//return JSON
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
