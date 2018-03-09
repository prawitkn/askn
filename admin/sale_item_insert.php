<?php
include 'session.php';/*$s_userID = $row_user['userID'];
        $s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_smCode = $row_user['smCode'];*/
include 'inc_helper.php';

try{
	
    $soNo = $_POST['soNo'];
	$deliveryDate = $_POST['deliveryDate'];
    $prodId = $_POST['prodId'];
    //$salesPrice = $_POST['salesPrice'];
	$qty = $_POST['qty'];
	$rollLengthId = $_POST['rollLengthId'];
	$remark = $_POST['remark'];
	//$total = $_POST['total'];
    //$discPercent = $_POST['discPercent'];
    //$discAmount = $_POST['discAmount'];
	//$netTotal = $_POST['netTotal'];
	
	$deliveryDate = to_mysql_date($deliveryDate);
	
	$pdo->beginTransaction();
	
	//update statusCode from A:Unknown to B:Begin
	$sql = "UPDATE`sale_header` SET statusCode='B'
	WHERE 1
	AND soNo=:soNo
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);	
	$stmt->execute();
	
	//insert product
    $sql = "INSERT INTO `sale_detail`
	(`prodId`, `deliveryDate`, `qty`, `rollLengthId`, `remark`, `createTime`, `soNo`) 
	VALUES 
	(:prodId, :deliveryDate, :qty,:rollLengthId,:remark, now(), :soNo)
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':prodId', $prodId);	
	$stmt->bindParam(':deliveryDate', $deliveryDate);	
	$stmt->bindParam(':qty', $qty);	
	$stmt->bindParam(':rollLengthId', $rollLengthId);	
	$stmt->bindParam(':remark', $remark);	
	$stmt->bindParam(':soNo', $soNo);	
	$stmt->execute();
		
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('status' => true, 'message' => 'Data Inserted Complete.'));
}catch(Exception $e){
	$pdo->rollBack();
	
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}

