<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID'];

	$sdNo = 'RM'.substr(str_shuffle(MD5(microtime())), 0, 8);
	$refNo = $_POST['refNo'];
	$sendDate = $_POST['sendDate'];
	$fromCode = $_POST['fromCode'];
	$toCode = $_POST['toCode'];
	
	$sendDate = to_mysql_date($sendDate);
	
	$sql = "INSERT INTO `send`
	(`sdNo`, `refNo`, `sendDate`, `fromCode`, `toCode`, `statusCode`, `createTime`, `createByID`) 
	VALUES
	(:sdNo,:refNo,:sendDate,:fromCode,:toCode,'B',now(),:s_userID)
			";
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $sdNo);
	$stmt->bindParam(':refNo', $refNo);
	$stmt->bindParam(':sendDate', $sendDate);
	$stmt->bindParam(':fromCode', $fromCode);
	$stmt->bindParam(':toCode', $toCode);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->execute();
			
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'sdNo' => $sdNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Inserte. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
