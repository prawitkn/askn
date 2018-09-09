<?php

include 'session.php';



//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	
	$soNo = $_POST['soNo'];
	$reason = $_POST['reason'];

	//Query 1: Check Status for not gen running No.
	$sql = "SELECT hdr.*, cust.locationCode FROM sale_header hdr
			INNER JOIN customer cust ON cust.id=hdr.custId 
			WHERE soNo=:soNo AND hdr.statusCode='P' LIMIT 1
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	$row=$stmt->fetch();
	$locationCode=$row['locationCode'];

	//We start our transaction.
	$pdo->beginTransaction();
	
	 
	//Log Header
	$sql = "INSERT INTO sale_rev_hdr  (`soNo`, `poNo`, `piNo`, `saleDate`, `custId`, `shipToId`, `smId`, `revCount`, `deliveryDate`
	, `shippingMarksId`, `shipByLcl`, `shipByFcl`, `shipByRem`, `deliveryRem`, `shippingTypeId`, `shippingRemark`, `shippingMark`
	, `sentDate`, `sentById`, `suppTypeFact`, `suppTypeImp`, `prodTypeOld`, `prodTypeNew`, `custTypeOld`, `custTypeNew`, `prodStkInStk`
	, `prodStkOrder`, `prodStkOther`, `prodStkRem`, `packTypeAk`, `packTypeNone`, `packTypeOther`, `packTypeRem`
	, `priceOnOrder`, `priceOnOther`, `priceOnRem`, `remCoa`, `remPalletBand`, `remFumigate`, `remark`
	, `plac2deliCode`, `plac2deliCodeSendRem`, `plac2deliCodeLogiRem`, `payTypeCode`, `payTypeCreditDays`, `isClose`, `statusCode`
	, `createTime`, `createById`, `updateTime`, `updateById`, `confirmTime`, `confirmById`, `approveTime`, `approveById`
	, `logRemark`, `logTime`, `logById`) 
	SELECT *,:logRemark, NOW(), :s_userId FROM sale_header hdr 
	WHERE hdr.soNo=:soNo 
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':logRemark', $reason);
    $stmt->bindParam(':soNo', $soNo);
	$stmt->bindParam(':s_userId', $s_userId);
    $stmt->execute();
	$logId = $pdo->lastInsertId();
	
	//Log Detail
	$sql = "INSERT INTO sale_rev_dtl (`id`, `prodId`, `deliveryDate`, `qty`, `rollLengthId`, `remark`, `salesPrice`
	, `total`, `discAmount`, `discPercent`, `discPerAmount`, `salesPerUnit`, `netTotal`
	, `createTime`, `soNo`, `logHdrId`)
	SELECT *,:logId FROM sale_detail dtl
	WHERE dtl.soNo=:soNo 
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':logId', $logId);
	$stmt->bindParam(':soNo', $soNo);	
    $stmt->execute();	
	
	//Query 1: UPDATE DATA
	$sql = "UPDATE sale_header SET statusCode='B'
	, revCount=revCount+1
	WHERE soNo=:soNo
	AND statusCode='P' 
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);
    $stmt->execute();
	

	$sloc=0;
	switch($locationCode){
		case 'L' : $sloc='8';
			break;
		case 'E' : $sloc='E';
			break;
		default :
	}
	//Query 5: UPDATE STK BAl	
	$sql = "UPDATE stk_bal tmp
	INNER JOIN (SELECT sd.prodId, -1*SUM(sd.qty) as sumQty
				FROM sale_detail sd  
				WHERE sd.soNo=:soNo 	
				GROUP BY sd.prodId) as x 
	SET tmp.sales=tmp.sales+x.sumQty
	WHERE tmp.prodId=x.prodId
	AND tmp.sloc=:sloc 		
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':soNo', $soNo);
    $stmt->bindParam(':sloc', $sloc);
    $stmt->execute();
	
	//Query 6: UPDATE STK BAl
	$sql = "INSERT INTO stk_bal (prodId, sloc, sales) 
			SELECT sd.prodId,:sloc, -1*SUM(sd.qty) FROM sale_detail sd 
			WHERE sd.soNo=:soNo 
			AND sd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sb.sloc=:sloc2 )
			GROUP BY sd.prodId
			";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':soNo', $soNo);
    $stmt->bindParam(':sloc', $sloc);
    $stmt->bindParam(':sloc2', $sloc);
    $stmt->execute();
    //Query 5: UPDATE STK BAl
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data revised', 'soNo' => $soNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data revise. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

