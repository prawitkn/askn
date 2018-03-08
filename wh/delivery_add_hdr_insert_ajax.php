<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
	  
	$doNo = 'DO-'.substr(str_shuffle(MD5(microtime())), 0, 7);
	$ppNo = $_POST['ppNo'];
	$deliveryDate = $_POST['deliveryDate'];
	$remark = $_POST['remark'];
	
	$deliveryDate = to_mysql_date($deliveryDate);
	
	$pdo->beginTransaction();
	
	$sql = "INSERT INTO `delivery_header`
	(`doNo`, `soNo`, `ppNo`, `deliveryDate`, `custId`, `shipToId`, `smId`, `remark`, `statusCode`, `createTime`, `createById`) 
	SELECT :doNo,oh.soNo,pp.ppNo,:deliveryDate,oh.custId,oh.shipToId,oh.smId,:remark,'B',now(),:s_userId 
	FROM sale_header oh
	INNER JOIN picking pk on pk.soNo=oh.soNo 
	INNER JOIN prepare pp on pp.pickNo=pk.pickNo 
	WHERE 1
	AND pp.ppNo=:ppNo 
			";

 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->bindParam(':ppNo', $ppNo);
	$stmt->bindParam(':deliveryDate', $deliveryDate);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userId', $s_userId);	
	$stmt->execute();
	
	//INsert Detail
	/*$sql = "INSERT INTO `delivery_detail`(`prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`
	, `NW`, `GW`, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`, `doNo`) 	 	
	SELECT `prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`
	, `NW`, `GW`, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`,:doNo 
	FROM prepare_detail  
	WHERE ppNo=:ppNo 
	";*/
	$sql = "INSERT INTO `delivery_detail`(`prodItemId`, `doNo`) 	 	
	SELECT `prodItemId`,:doNo 
	FROM prepare_detail  
	WHERE ppNo=:ppNo 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->bindParam(':ppNo', $ppNo);		
	$stmt->execute();
	
	//`prodId`, `itemCount`, `qty`, `remark`, `doNo`
	$sql = "INSERT INTO `delivery_prod`(`prodId`, `itemCount`, `qty`, `remark`, `doNo`) 	 	
	SELECT itm.prodCodeId, COUNT(*), SUM(itm.qty), '',:doNo 
	FROM prepare_detail pDtl 
	LEFT JOIN product_item itm ON itm.prodItemId=pDtl.prodItemId 
	WHERE ppNo=:ppNo 
	GROUP BY pDtl.prodId
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->bindParam(':ppNo', $ppNo);		
	$stmt->execute();
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
			
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'doNo'=>$doNo ));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback
	$pdo->rollback();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
