<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	   
	$soNo = $_POST['soNo'];	
	$pickNo = 'PI-'.substr(str_shuffle(MD5(microtime())), 0, 7);
	$pickDate = $_POST['pickDate'];
	$remark = $_POST['remark'];
	
	$pickDate = to_mysql_date($pickDate);
		
	$sql = "INSERT INTO `picking`
	(`pickNo`, `soNo`, `pickDate`, `remark`, `statusCode`, `createTime`, `createById`) 
	VALUES (:pickNo,:soNo,:pickDate,:remark,'B',now(),:s_userId)
	";
			
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':pickNo', $pickNo);
	$stmt->bindParam(':pickDate', $pickDate);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userId', $s_userId);	
	$stmt->bindParam(':soNo', $soNo);
	$stmt->execute();
			
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'pickNo'=> $pickNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
