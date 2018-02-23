<?php	
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode
include 'inc_helper.php';

try{
    //$rcNo = $_POST['rcNo'];	
	$refNo = $_POST['refNo'];	
	$receiveDate = $_POST['receiveDate'];	
	$remark = $_POST['remark'];	
	
	$receiveDate = to_mysql_date($receiveDate);
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT docNo FROM inv_ret WHERE docNo=:refNo AND statusCode='P' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':refNo', $refNo);
	$stmt->execute();
	$hdr = $stmt->fetch();	
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}	
	
	$rcNo = 'RC-'.substr(str_shuffle(MD5(microtime())), 0, 7);
	//Query 1: DELETE Detail
	$sql = "INSERT INTO `receive`(`rcNo`, `receiveDate`, `type`, `fromCode`, `toCode`, `remark`, `refNo`, `statusCode`, `createByID`) 
	VALUES (:rcNo,:receiveDate,'C', 'C', '8',:remark,:refNo, 'B',:s_userID) 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);	
	$stmt->bindParam(':receiveDate', $receiveDate);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userID', $s_userID);
	$stmt->bindParam(':refNo', $refNo);		
	$stmt->execute();
	
	//INsert Detail
	$sql = "INSERT INTO `receive_detail`(`prodItemId`, `rcNo`) 	 	
	SELECT `prodItemId`, :rcNo 
	FROM inv_ret_detail  
	WHERE docNo=:refNo  
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);
	$stmt->bindParam(':refNo', $refNo);		
	$stmt->execute();
				
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
		
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data deleted', 'rcNo' => $rcNo));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback
	$pdo->rollback();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


