<?php
include 'inc_helper.php';  
include 'session.php';	/*$s_userID=$_SESSION['userID'];
		$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
	
try{
	
	$docNo = 'CN-'.substr(str_shuffle(MD5(microtime())), 0, 7);
	$docDate = $_POST['docDate'];
	$refNo = $_POST['refNo'];
	$remark = $_POST['remark'];
	
	$docDate = to_mysql_date($docDate);
	
	$sql = "INSERT INTO `inv_ret`
	(`docNo`, `docDate`, `refNo`, `custCode`, `smCode`, `remark`,  `statusCode`, `createTime`, `createByID`) 
	SELECT :docNo,:docDate,rc.invNo,rc.custCode,rc.smCode,:remark,'B',now(),:s_userID 
	FROM invoice_header rc 
	WHERE rc.invNo=:refNo 
	";
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':docNo', $docNo);
	$stmt->bindParam(':docDate', $docDate);
	$stmt->bindParam(':refNo', $refNo);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->execute();
			
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'docNo' => $docNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Inserted. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
