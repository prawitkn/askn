<?php
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode

//Check user roll.
switch($s_userGroupCode){
	case 'admin' : case 'salesAdmin' : 
		break;
	default : 
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
		exit();
}

try{
    $invNo = $_POST['invNo'];	
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT invNo FROM invoice_header WHERE invNo=:invNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':invNo', $invNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'invoice'; $prefix = 'IV'.date('y'); $cur_no=1;
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
	
	//Query 1: UPDATE DATA HEADER
	$sql = "UPDATE `invoice_header` SET statusCode='P'
			, invNo=:nextNo
			, approveTime=now()
			, approveById=:s_userID 
			WHERE invNo=:invNo";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':s_userID', $s_userID);
	$stmt->bindParam(':invNo', $invNo);
    $stmt->execute();
		
	//Query 2: UPDATE DATA DETAIL
	$sql = "UPDATE invoice_detail SET invNo=:nextNo WHERE invNo=:invNo ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':invNo', $invNo);
    $stmt->execute();
	
	//Query 2: UPDATE DATA DELIVERY
	$sql = "UPDATE delivery_header dh
				INNER JOIN invoice_header ih on ih.doNo=dh.doNo AND ih.invNo=:invNo 
			SET refInvNo=:nextNo ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':invNo', $nextNo);
    $stmt->execute();	
	
    //UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data approved', 'invNo' => $nextNo));	
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


