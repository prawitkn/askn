<?php
include 'inc_helper.php';  
include 'session.php';	
   
try{   
    $visitDate = $_POST['visitDate'];
    $smCode= $_POST['smCode'];
    $custCode = $_POST['custCode'];
    $custContactName = $_POST['custContactName'];
    $custContactTelNo = $_POST['custContactTelNo'];
	$visitTypeCode = $_POST['visitTypeCode'];
	$remark = $_POST['remark'];
	
	$visitDate = to_mysql_date($visitDate);
	
	//We start our transaction.
	$pdo->beginTransaction();	
	
	//Query Doc No. : GET Next Doc No.
	$year = date('Y'); $name = 'visitCust'; $prefix = 'VC'.date('y'); $cur_no=1;
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
	$orderNoNext = $prefix . substr($next_no, -6);
	
	//Query 1: Insert Data
    $sql = "
		INSERT INTO `visit_customer`(`smCode`, `custCode`, `visitDate`, `custContactName`, `custContactTelNo`, `visitTypeCode`, `remark`, `statusCode`, `createByID`) 
		VALUES 
		(:smCode, :custCode, :visitDate, :custContactName, :custContactTelNo, :visitTypeCode, :remark, 'A', :s_userID)
		";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':smCode', $smCode);	
	$stmt->bindParam(':custCode', $custCode);	
	$stmt->bindParam(':visitDate', $visitDate);	
	$stmt->bindParam(':custContactName', $custContactName);	
	$stmt->bindParam(':custContactTelNo', $custContactTelNo);	
	$stmt->bindParam(':visitTypeCode', $visitTypeCode);	
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->execute();	
	//Get id
	$id = $pdo->lastInsertId();
	
	//Query 2: UPDATE running no.
	$sql = "UPDATE visit_customer SET visitNo=:visitNo WHERE id=:id ";
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':visitNo', $orderNoNext);
	$stmt->bindParam(':id', $id);
	$stmt->execute();	
	
	//Query Doc No. : Update doc running.
	$sql = "UPDATE doc_running SET cur_no=:cur_no WHERE year=:year and name=:name ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':cur_no', $cur_no);
	$stmt->bindParam(':year', $year);
	$stmt->bindParam(':name', $name);	
	$stmt->execute();
	
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
	//Return JSON.
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
}catch (Exception $e){  
	//Rollback the transaction.
    $pdo->rollBack();
	//Return JSON.
    header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}

