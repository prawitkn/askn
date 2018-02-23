<?php
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode

//Check user roll.
switch($s_userGroupCode){
	case 'it' : case 'admin' : case 'whSup' : 
		break;
	default : 
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
		exit();
}

try{
    $doNo = $_POST['doNo'];	
	$soNo = $_POST['soNo'];
	$isClose = $_POST['isClose'];	
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT doNo FROM delivery_header WHERE doNo=:doNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'delivery'; $prefix = 'DO'.date('y'); $cur_no=1;
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
	$sql = "UPDATE `delivery_header` SET statusCode='P'
			, doNo=:nextNo
			, approveTime=now()
			, approveById=:s_userId 
			WHERE doNo=:doNo";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':s_userId', $s_userId);
	$stmt->bindParam(':doNo', $doNo);
    $stmt->execute();
		
	//Query 2: UPDATE DATA
	$sql = "UPDATE delivery_detail SET doNo=:nextNo WHERE doNo=:doNo ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':doNo', $doNo);
    $stmt->execute();
	
    //UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));
	
	//Close Sales Order.
	if($isClose=='Yes'){
		$sql = "UPDATE sale_header SET isClose='Y' WHERE soNo=:soNo ";
		$stmt = $pdo->prepare($sql);		
		$stmt->bindParam(':soNo', $soNo);
		$stmt->execute();
	}
	
	//Query 5: UPDATE STK BAl
	$sql = "UPDATE stk_bal sb
			INNER JOIN product_item itm ON itm.prodCodeId=sb.prodId 
			INNER JOIN delivery_detail dd on dd.prodItemId=itm.prodItemId 			
			SET sb.delivery = sb.delivery + dd.qty 
			, sb.sales = sb.sales - dd.qty 
			, sb.balance = sb.balance - dd.qty 
			WHERE dd.doNo=:nextNo 
			AND sb.sloc='8' 
			";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nextNo', $nextNo);
    $stmt->execute();
	
	//Query 6: UPDATE STK BAl
	$sql = "INSERT INTO stk_bal (prodId, sloc, delivery, sales, balance) 
			SELECT dd.prodId,'8', dd.qty, -1*dd.qty, -1*dd.qty  FROM delivery_detail dd 
			WHERE dd.doNo=:nextNo 
			AND dd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sloc='8' )
			";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nextNo', $nextNo);
    $stmt->execute();
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data approved', 'doNo' => $nextNo));	
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


