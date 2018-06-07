<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	
	$s_userId = $_SESSION['userId'];
   
	$pickNo = $_POST['pickNo'];	
	$ppNo = 'PP-'.substr(str_shuffle(MD5(microtime())), 0, 7);
	$prepareDate = $_POST['prepareDate'];
	$remark = $_POST['remark'];
	
	$prepareDate = to_mysql_date($prepareDate);
		
	$sql = "INSERT INTO `prepare`
	(`ppNo`, `pickNo`, `prepareDate`, `remark`, `statusCode`, `createTime`, `createByID`) 
	VALUES (:ppNo,:pickNo,:prepareDate,:remark,'B',now(),:s_userId)
	";
			
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':pickNo', $pickNo);
	$stmt->bindParam(':prepareDate', $prepareDate);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userId', $s_userId);	
	$stmt->bindParam(':ppNo', $ppNo);
	$stmt->execute();
	
	//unset($_SESSION['ppData']);
	//Delete scanned
	$sql = "DELETE FROM prepare_scan WHERE userId=:s_userId
			";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':s_userId', $s_userId);
	$stmt->execute();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'ppNo'=> $ppNo ));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
