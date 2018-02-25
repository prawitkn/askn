<?php

include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

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

$rtNo = $_POST['rtNo'];

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{
	//We start our transaction.
	$pdo->beginTransaction();
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM rt WHERE rtNo=:rtNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rtNo', $rtNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	$hdr=$stmt->fetch();
	$fromCode = $hdr['fromCode'];
	$toCode = $hdr['toCode'];
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'return'; $prefix = 'RT'.date('y').$fromCode; $cur_no=1;
	$sql = "SELECT prefix, cur_no FROM doc_running WHERE year=? and name=?  and prefix=?  LIMIT 1";
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
	$sql = "UPDATE rt SET statusCode='P'
	, rtNo=:noNext  
	, approveTime=now()
	, approveById=:approveById
	WHERE rtNo=:rtNo  
	AND statusCode='C' 
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':noNext', $noNext);
	$stmt->bindParam(':approveById', $s_userId);
	$stmt->bindParam(':rtNo', $rtNo);
    $stmt->execute();
		
	//Query 3: UPDATE DATA
	$sql = "UPDATE rt_detail SET rtNo=? WHERE rtNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($noNext,$rtNo));
	
    //Query 4:  UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));	
	
	
	
	
	
	
	
	//Query 5: UPDATE STK BAl sloc from 
	$sql = "		
	UPDATE stk_bal sb,
	( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
		   FROM rt_detail dtl
		   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
		   WHERE rtNo=:rtNo GROUP BY itm.prodCodeId) as s
	SET sb.send=sb.send+s.sumQty
	, sb.balance=sb.balance-s.sumQty 
	WHERE sb.prodId=s.prodCodeId
	AND sb.sloc=:fromCode
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rtNo', $noNext);
	$stmt->bindParam(':fromCode', $fromCode);
    $stmt->execute();
		
	//Query 6: INSERT STK BAl sloc from 
	$sql = "INSERT INTO stk_bal (prodId, sloc, send, balance) 
	SELECT itm.prodCodeId, :fromCode, SUM(itm.qty), -1*SUM(itm.qty) 
	FROM rt_detail sd
	INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
	WHERE sd.rtNo=:rtNo 
	AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:fromCode2)
	GROUP BY itm.prodCodeId
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rtNo', $noNext);
    $stmt->bindParam(':fromCode', $fromCode);
	$stmt->bindParam(':fromCode2', $fromCode);
    $stmt->execute();
	
	//Query 5: UPDATE STK BAl sloc to 
	$sql = "		
	UPDATE stk_bal sb,
	( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
		   FROM rt_detail dtl
		   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
		   WHERE rtNo=:rtNo GROUP BY itm.prodCodeId) as s
	SET sb.onway=sb.onway+s.sumQty
	WHERE sb.prodId=s.prodCodeId
	AND sb.sloc=:toCode
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rtNo', $noNext);
	$stmt->bindParam(':toCode', $toCode);
    $stmt->execute();
	
	//Query 6: INSERT STK BAl sloc to 
	$sql = "INSERT INTO stk_bal (prodId, sloc, onway) 
			SELECT itm.prodCodeId, :toCode, SUM(itm.qty) 
			FROM rt_detail sd 
			INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
			WHERE sd.rtNo=:rtNo 
			AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
			GROUP BY itm.prodCodeId
			";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rtNo', $noNext);
    $stmt->bindParam(':toCode', $toCode);
	$stmt->bindParam(':toCode2', $toCode);
    $stmt->execute();
	
	
	
	
	
	
	
	//Query 3: UPDATE Shelf
	$sql = "DELETE wsi 
	FROM wh_sloc_map_item wsi
	INNER JOIN receive_detail rcDtl ON rcDtl.id=wsi.recvProdId  
	INNER JOIN rt_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.rtNo=:rtNo
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rtNo', $noNext);
    $stmt->execute();
	
	//Query 3: UPDATE Receive Detail 
	$sql = "UPDATE receive_detail rcDtl 
	INNER JOIN rt_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.rtNo=:rtNo
	SET rcDtl.statusCode='A', rcDtl.isReturn='Y', shelfCode='' 
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':rtNo', $noNext);
    $stmt->execute();
	
	
	
	
			
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data approved', 'rtNo' => $noNext));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data approval. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

