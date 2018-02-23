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
    $prodCode = $_POST['prodCode'];
    $salesPrice = $_POST['salesPrice'];
	$qty = $_POST['qty'];
	$total = $_POST['total'];
    $discPercent = $_POST['discPercent'];
    $discAmount = $_POST['discAmount'];
	$netTotal = $_POST['netTotal'];
	
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
	(`prodCode`, `deliveryDate`, `salesPrice`, `qty`, `total`, `discPercent`, `discAmount`, `netTotal`, `createTime`, `soNo`) 
	VALUES 
	(:prodCode, :deliveryDate, :salesPrice, :qty, :total, :discPercent, :discAmount, :netTotal, now(), :soNo)
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':prodCode', $prodCode);	
	$stmt->bindParam(':deliveryDate', $deliveryDate);	
	$stmt->bindParam(':salesPrice', $salesPrice);	
	$stmt->bindParam(':qty', $qty);	
	$stmt->bindParam(':total', $total);	
	$stmt->bindParam(':discPercent', $discPercent);	
	$stmt->bindParam(':discAmount', $discAmount);	
	$stmt->bindParam(':netTotal', $netTotal);	
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

