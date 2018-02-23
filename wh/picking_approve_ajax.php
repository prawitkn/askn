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
    $pickNo = $_POST['pickNo'];	
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT pickNo FROM picking WHERE pickNo=:pickNo AND statusCode='C' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':pickNo', $pickNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'picking'; $prefix = 'Pi'.date('y'); $cur_no=1;
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
	$sql = "UPDATE `picking` SET statusCode='P'
			, pickNo=:nextNo
			, approveTime=now()
			, approveById=:s_userID 
			WHERE pickNo=:pickNo";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':s_userID', $s_userID);
	$stmt->bindParam(':pickNo', $pickNo);
    $stmt->execute();
		
	//Query 2: UPDATE DATA
	$sql = "UPDATE picking_detail SET pickNo=:nextNo WHERE pickNo=:pickNo ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':nextNo', $nextNo);
	$stmt->bindParam(':pickNo', $pickNo);
    $stmt->execute();
	
    //UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));
		
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data approved', 'pickNo' => $nextNo));	
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


