<?php

include 'session.php';

//Check user roll.
switch($s_userGroupCode){
	case 'it' : case 'admin' : case 'salesAdmin' : 
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
	$sql = "SELECT hdr.*, cust.locationCode FROM sale_header hdr
			INNER JOIN customer cust ON cust.id=hdr.custId 
			WHERE soNo=:soNo AND hdr.statusCode='C' LIMIT 1";
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
	$soNoNext = '';
	if($row['revCount']<>0){
		$soNoNext = $row['soNo'];
		//Query 2: UPDATE Header
		$sql = "UPDATE sale_header SET statusCode='P'   
			, approveTime=now()
			, approveById=?
			WHERE soNo=? ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(		
				$s_userId,
				$soNoNext	
			)
		);
	}else{//end if revised.
		$year = date('Y'); $name = ''; $prefix = ''; $cur_no=1;
		switch($locationCode){
			case 'L' : $name='saleLocal'; $prefix = 'SO'.date('y'); 
				break;
			case 'E' : $name='saleExport'; $prefix = 'SOE'.date('y'); 
				break;
			default :
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'locationCode incorrect.'));
				exit();
		}	
		//Query 1: GET Next Doc No.
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
		$soNoNext = '';
		switch($locationCode){
			case 'L' : $soNoNext = $prefix . substr($next_no, -4);
				break;
			case 'E' : $soNoNext = $prefix . substr($next_no, -3);
				break;
			default :
		}
		
		//Query 2: UPDATE Header
		$sql = "UPDATE sale_header SET statusCode='P'   
			, soNo=?
			, approveTime=now()
			, approveById=?
			WHERE soNo=? ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array(		
				$soNoNext,
				$s_userId,
				$soNo	
			)
		);
		
		//Query 3: UPDATE Detail
		$sql = "UPDATE sale_detail SET soNo=? WHERE soNo=? ";
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array($soNoNext,$soNo));
		
		//Query 4:  UPDATE doc running.
		$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
		$stmt = $pdo->prepare($sql);		
		$stmt->execute(array($cur_no, $year, $name));
	}//end if not revised
	
	
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
	INNER JOIN (SELECT sd.prodId, SUM(sd.qty) as sumQty
				FROM sale_detail sd  
				WHERE sd.soNo=:soNo 	
				GROUP BY sd.prodId) as x 
	SET tmp.sales=tmp.sales+x.sumQty
	WHERE tmp.prodId=x.prodId
	AND tmp.sloc=:sloc 		
	";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':soNo', $soNoNext);
    $stmt->bindParam(':sloc', $sloc);
    $stmt->execute();
	
	//Query 6: UPDATE STK BAl
	$sql = "INSERT INTO stk_bal (prodId, sloc, sales) 
			SELECT sd.prodId,:sloc, SUM(sd.qty) FROM sale_detail sd 
			WHERE sd.soNo=:soNo 
			AND sd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sb.sloc=:sloc2 )
			GROUP BY sd.prodId
			";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':soNo', $soNoNext);
    $stmt->bindParam(':sloc', $sloc);
    $stmt->bindParam(':sloc2', $sloc);
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

