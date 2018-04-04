<?php	
include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
include 'inc_helper.php';

try{
    $sdNo = $_POST['sdNo'];	
	//$refNo = $_POST['refNo'];	
	$receiveDate = $_POST['receiveDate'];	
	$remark = $_POST['remark'];	
	
	$receiveDate = to_mysql_date($receiveDate);
	//We start our transaction.
	$pdo->beginTransaction();
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT sdNo FROM send WHERE sdNo=:sdNo AND statusCode='P' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':sdNo', $sdNo);
	$stmt->execute();
	$hdr = $stmt->fetch();	
	$row_count = $stmt->rowCount();	
	if($row_count != 1 ){		
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}	
	
	$rcNo = 'RC-'.substr(str_shuffle(MD5(microtime())), 0, 7);
	//Query 1: DELETE Detail
	$sql = "INSERT INTO `receive`(`rcNo`, `receiveDate`, `type`, `fromCode`, `toCode`, `remark`, `sdNo`, `statusCode`, `createByID`) 
	SELECT :rcNo,:receiveDate, 'S',`fromCode`, `toCode`,:remark,`sdNo`, 'B',:s_userId  
	FROM send 
	WHERE sdNo=:sdNo 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);	
	$stmt->bindParam(':receiveDate', $receiveDate);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userId', $s_userId);
	$stmt->bindParam(':sdNo', $sdNo);		
	$stmt->execute();
	
	//INsert Detail
	$sql = "INSERT INTO `receive_detail`(`prodItemId`, `statusCode`, `rcNo`) 	 	
	SELECT `prodItemId`, 'A', :rcNo 
	FROM send_detail  
	WHERE sdNo=:sdNo 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);
	$stmt->bindParam(':sdNo', $sdNo);		
	$stmt->execute();
				
	//We've got this far without an exception, so commit the changes.
    $pdo->commit();
		
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data deleted', 'rcNo' => $rcNo));	
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback
	$pdo->rollback();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


