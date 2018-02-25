<?php

include 'session.php';

//Check user roll.
switch($s_userGroupCode){
	case 'it' : case 'admin' : case 'whSup' : case 'pdSup' : 
		break;
	default : 
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
		exit();
}

$rcNo = $_POST['rcNo'];

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{
	//We start our transaction.
	$pdo->beginTransaction();
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM receive WHERE rcNo=:rcNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();
	if($row_count != 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	$row = $stmt->fetch();
	$toCode = $row['toCode'];
	$refNo = $row['refNo'];
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'receive'; $prefix = 'RR'.date('y').$toCode; $cur_no=1;
	$sql = "SELECT prefix, cur_no FROM doc_running WHERE year=? and name=? and prefix=? LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($year, $name, $prefix));
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
	$noNext = $prefix . substr($next_no, -5);
	
	//Query 1: UPDATE DATA
	$sql = "UPDATE receive SET statusCode='P'
	, rcNo=:noNext  
	, approveTime=now()
	, approveById=:approveById
	WHERE rcNo=:rcNo  
	AND statusCode='C' 
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':noNext', $noNext);
	$stmt->bindParam(':approveById', $s_userId);
	$stmt->bindParam(':rcNo', $rcNo);
    $stmt->execute();
		
	//Query 3: UPDATE DATA
	$sql = "UPDATE receive_detail SET rcNo=? WHERE rcNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($noNext,$rcNo));
	
	//Query 3: UPDATE Return
	$sql = "UPDATE rt SET rcNo=? WHERE rtNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($noNext,$refNo));
	
    //Query 4:  UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));
	
	
	
	
	
	//Query 5: UPDATE STK BAl sloc to 
	$sql = "		
	UPDATE stk_bal sb,
	( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
		   FROM receive_detail dtl
		   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
		   WHERE rcNo=:rcNo GROUP BY itm.prodCodeId) as s
	SET sb.onway=sb.onway-s.sumQty
	, sb.receive=sb.receive+s.sumQty
	, sb.balance=sb.balance+s.sumQty 
	WHERE sb.prodId=s.prodCodeId
	AND sb.sloc=:toCode
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rcNo', $noNext);
	$stmt->bindParam(':toCode', $toCode);
    $stmt->execute();
		
	//Query 6: INSERT STK BAl sloc to 
	$sql = "INSERT INTO stk_bal (prodId, sloc, onway, receive, balance) 
	SELECT itm.prodCodeId, :toCode, -1*SUM(itm.qty), SUM(itm.qty), -1*SUM(itm.qty) 
	FROM receive_detail dtl
	INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
	WHERE dtl.rcNo=:rcNo 
	AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
	GROUP BY itm.prodCodeId
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $noNext);
    $stmt->bindParam(':toCode', $toCode);
	$stmt->bindParam(':toCode2', $toCode);
    $stmt->execute();
	
	
	
	
	
					
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data approved', 'rcNo' => $noNext));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on data approval. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

