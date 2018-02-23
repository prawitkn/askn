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

try{
    $wipNo = $_POST['wipNo'];	
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM wip WHERE wipNo=:wipNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':wipNo', $wipNo);
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
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'WIP'; $prefix = 'WP'.date('y'); $cur_no=1;
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
	$nextNo = $prefix . substr($next_no, -6);
	
	//Query 1: UPDATE DATA
	$sql = "UPDATE `wip` SET statusCode='P'
			, wipNo=:nextNo
			, approveTime=now()
			, approveById=:s_userID 
			WHERE wipNo=:wipNo";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':s_userID', $s_userID);
	$stmt->bindParam(':wipNo', $wipNo);
    $stmt->execute();
		
	//Query 2: UPDATE DATA
	$sql = "UPDATE wip_detail SET wipNo=:nextNo WHERE wipNo=:wipNo ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':wipNo', $wipNo);
    $stmt->execute();
	
    //UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));
		
	//Query 3: UPDATE Shelf
	/*$sql = "DELETE wsi 
	FROM wh_sloc_map_item wsi
	INNER JOIN receive_detail rcDtl ON rcDtl.id=wsi.recvProdId  
	INNER JOIN rt_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.rtNo=:rtNo
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rtNo', $noNext);
    $stmt->execute();*/
	
	//Query 3: UPDATE Receive Detail 
	$sql = "UPDATE receive_detail rcDtl 
	INNER JOIN wip_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.refNo=rcDtl.rcNo 
	SET rcDtl.statusCode='P' 
	WHERE rtDtl.wipNo=:nextNo 
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nextNo', $nextNo);
    $stmt->execute();
	
	
	
	
	
	//Query 5: UPDATE STK BAl sloc from 
	$sql = "		
	UPDATE stk_bal sb,
	( SELECT itm.prodCode, sum(itm.qty)  as sumQty
		   FROM wip_detail dtl
		   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
		   WHERE wipNo=:wipNo GROUP BY itm.prodCode) as s
	SET sb.wip=sb.wip+s.sumQty
	, sb.balance=sb.balance-s.sumQty 
	WHERE sb.prodCode=s.prodCode
	AND sb.sloc=:fromCode
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':wipNo', $nextNo);
	$stmt->bindParam(':fromCode', $fromCode);
    $stmt->execute();
		
	//Query 6: INSERT STK BAl sloc from 
	$sql = "INSERT INTO stk_bal (prodCode, sloc, wip, balance) 
	SELECT itm.prodCode, :fromCode, SUM(itm.qty), -1*SUM(itm.qty) 
	FROM wip_detail sd
	INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
	WHERE sd.wipNo=:wipNo 
	AND itm.prodCode NOT IN (SELECT sb2.prodCode FROM stk_bal sb2 WHERE sb2.sloc=:fromCode2)
	GROUP BY itm.prodCode
	";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':wipNo', $nextNo);
    $stmt->bindParam(':fromCode', $fromCode);
	$stmt->bindParam(':fromCode2', $fromCode);
    $stmt->execute();
	
	
	
	
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
	unset($_SESSION['ppData']);
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data approved', 'wipNo' => $nextNo));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Approve. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


