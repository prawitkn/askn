<?php

include 'session.php';

//Check user roll.
switch($s_userGroupCode){
	case 'id' : case 'admin' : case 'whSup' : case 'pdSup' :
		break;
	default : 
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
		exit();
}

$sdNo = $_POST['sdNo'];

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{
	//We start our transaction.
	$pdo->beginTransaction();
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM send WHERE sdNo=:sdNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $sdNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	$hdr = $stmt->fetch();
	if($row_count != 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	$fromCode = $hdr['fromCode'];
	$toCode = $hdr['toCode'];
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'send'; $prefix = 'RM'.date('y'); $cur_no=1;
	$sql = "SELECT prefix, cur_no FROM doc_running WHERE year=? and name=? LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($year, $name));
	$row_count = $stmt->rowCount();	
    if($row_count == 0){
		$sql = "INSERT INTO doc_running (year, name, prefix, cur_no) VALUES (?,?,?,?)";
		$stmt = $pdo->prepare($sql);		
		$stmt->execute(array($year, $name, $prefix, $cur_no));
	}else{
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$prefix = $row['prefix'];
		$cur_no = (int)$row['cur_no']+1;		
	}
	$next_no = '00000'.(string)$cur_no;
	$noNext = $prefix . substr($next_no, -6);
	
	//Query 1: UPDATE DATA
	$sql = "UPDATE send SET statusCode='P'
	, sdNo=:noNext  
	, approveTime=now()
	, approveById=:approveById
	WHERE sdNo=:sdNo  
	AND statusCode='C' 
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':noNext', $noNext);
	$stmt->bindParam(':approveById', $s_userId);
	$stmt->bindParam(':sdNo', $sdNo);
    $stmt->execute();
		
	//Query 3: UPDATE DATA
	$sql = "UPDATE send_detail SET sdNo=? WHERE sdNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($noNext,$sdNo));
	
    //Query 4:  UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));	
	
	
	
	
	//Query 5: UPDATE STK BAl sloc from 
	$sql = "		
	UPDATE stk_bal sb,
	( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
		   FROM send_detail dtl
		   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
		   WHERE sdNo=:sdNo GROUP BY itm.prodCodeId) as s
	SET sb.send=sb.send+s.sumQty
	, sb.balance=sb.balance-s.sumQty 
	WHERE sb.prodId=s.prodCodeId
	AND sb.sloc=:fromCode
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sdNo', $noNext);
	$stmt->bindParam(':fromCode', $fromCode);
    $stmt->execute();
		
	//Query 6: INSERT STK BAl sloc from 
	$sql = "INSERT INTO stk_bal (prodId, sloc, send, balance) 
	SELECT itm.prodCodeId, :fromCode, SUM(itm.qty), -1*SUM(itm.qty) 
	FROM send_detail sd
	INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
	WHERE sd.sdNo=:sdNo 
	AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:fromCode2)
	GROUP BY itm.prodCodeId
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $noNext);
    $stmt->bindParam(':fromCode', $fromCode);
	$stmt->bindParam(':fromCode2', $fromCode);
    $stmt->execute();
	
	//Query 5: UPDATE STK BAl sloc to 
	$sql = "		
	UPDATE stk_bal sb,
	( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
		   FROM send_detail dtl
		   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
		   WHERE sdNo=:sdNo GROUP BY itm.prodCodeId) as s
	SET sb.onway=sb.onway+s.sumQty
	WHERE sb.prodId=s.prodCodeId
	AND sb.sloc=:toCode
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':sdNo', $noNext);
	$stmt->bindParam(':toCode', $toCode);
    $stmt->execute();
	
	//Query 6: INSERT STK BAl sloc to 
	$sql = "INSERT INTO stk_bal (prodId, sloc, onway) 
			SELECT itm.prodCodeId, :toCode, SUM(itm.qty) 
			FROM send_detail sd 
			INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
			WHERE sd.sdNo=:sdNo 
			AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
			GROUP BY itm.prodCodeId
			";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $noNext);
    $stmt->bindParam(':toCode', $toCode);
	$stmt->bindParam(':toCode2', $toCode);
    $stmt->execute();
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data Approved', 'sdNo' => $noNext));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Approval. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

