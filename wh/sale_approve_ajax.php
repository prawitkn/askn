<?php

include 'session.php';

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

$soNo = $_POST['soNo'];

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{
	//We start our transaction.
	$pdo->beginTransaction();
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM sale_header WHERE soNo=:soNo AND statusCode='C' LIMIT 1";
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
	
	//Query 1: GET Next Doc No.
	$year = date('Y'); $name = 'sale'; $prefix = 'SO'.date('y'); $cur_no=1;
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
	$soNoNext = $prefix . substr($next_no, -6);
	
	//Query 1: UPDATE DATA
	$sql = "UPDATE sale_header SET statusCode='P'
			WHERE soNo=:soNo
			AND statusCode='C' 
		";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);
    $stmt->execute();
	
	//Query 2: UPDATE DATA
	$sql = "UPDATE sale_header SET statusCode='P'   
		, soNo=? 
		, approveTime=now()
		, approveById=?
		WHERE soNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(		
			$soNoNext,
			$s_userID,
			$soNo	
        )
    );
	
	//Query 3: UPDATE DATA
	$sql = "UPDATE sale_detail SET soNo=? WHERE soNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array($soNoNext,$soNo));
	
    //Query 4:  UPDATE doc running.
	$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
	$stmt = $pdo->prepare($sql);		
	$stmt->execute(array($cur_no, $year, $name));
	
	//Query 5: UPDATE STK BAl
	$sql = "UPDATE stk_bal sb
			INNER JOIN sale_detail sd on sd.prodCode=sb.prodCode 
			SET sb.sales = sb.sales + sd.qty 
			WHERE sd.soNo=:soNo 
			";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':soNo', $soNoNext);
    $stmt->execute();
	
	//Query 6: UPDATE STK BAl
	$sql = "INSERT INTO stk_bal (prodCode, sales) 
			SELECT sd.prodCode, sd.qty FROM sale_detail sd 
			WHERE sd.soNo=:soNo 
			AND sd.prodCode NOT IN (SELECT sb.prodCode FROM stk_bal sb)
			";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':soNo', $soNoNext);
    $stmt->execute();
			
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data approved', 'soNo' => $soNoNext));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data approve. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

