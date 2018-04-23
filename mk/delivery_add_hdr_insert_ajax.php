<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID'];
   
	$soNo = $_POST['soNo'];	
	$doNo = substr(str_shuffle(MD5(microtime())), 0, 10);
	$refNo = $_POST['refNo'];
	$deliveryDate = $_POST['deliveryDate'];
	$remark = $_POST['remark'];
	
	$deliveryDate = to_mysql_date($deliveryDate);
	
	$sql = "INSERT INTO `delivery_header`
	(`doNo`, `soNo`, `refNo`, `deliveryDate`, `custCode`, `shipToCode`, `smCode`, `remark`, `statusCode`, `createTime`, `createByID`) 
	SELECT :doNo,oh.soNo,:refNo,:deliveryDate,oh.custCode,oh.shipToCode,oh.smCode,:remark,'B',now(),:s_userID 
	FROM sale_header oh
	WHERE 1
	AND soNo=:soNo 
			";
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->bindParam(':refNo', $refNo);
	$stmt->bindParam(':deliveryDate', $deliveryDate);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->bindParam(':soNo', $soNo);
	$stmt->execute();
			
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
