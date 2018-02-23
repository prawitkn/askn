<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID'];

	$rcNo = 'R-'.substr(str_shuffle(MD5(microtime())), 0, 8);
	$refNo = $_POST['refNo'];
	$receiveDate = $_POST['receiveDate'];
	$fromCode = $_POST['fromCode'];
	$remark = $_POST['remark'];
	
	$receiveDate = to_mysql_date($receiveDate);
	
	$sql = "INSERT INTO `receive`
	(`rcNo`, `refNo`, `receiveDate`, `fromCode`, `remark`, `statusCode`, `createTime`, `createByID`) 
	VALUES
	(:rcNo,:refNo,:receiveDate,:fromCode,:remark,'B',now(),:s_userID)
			";
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);
	$stmt->bindParam(':refNo', $refNo);
	$stmt->bindParam(':receiveDate', $receiveDate);
	$stmt->bindParam(':fromCode', $fromCode);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->execute();
			
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'rcNo' => $rcNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
