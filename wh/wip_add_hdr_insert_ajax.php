<?php
include 'inc_helper.php';  
include 'session.php';	/*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];*/
	
try{
	$wipNo = 'WP-'.substr(str_shuffle(MD5(microtime())), 0, 7);
	$wipDate = $_POST['wipDate'];
	$remark = $_POST['remark'];
	
	$wipDate = to_mysql_date($wipDate);
		
	$sql = "INSERT INTO `wip`
	(`wipNo`, `wipDate`, `fromCode`, `remark`, `statusCode`, `createTime`, `createByID`) 
	VALUES (:wipNo,:wipDate,:fromCode,:remark,'B',now(),:s_userID)
	";
			
 	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':wipDate', $wipDate);
	$stmt->bindParam(':fromCode', $s_userDeptCode);
	$stmt->bindParam(':remark', $remark);
	$stmt->bindParam(':s_userID', $s_userID);	
	$stmt->bindParam(':wipNo', $wipNo);
	$stmt->execute();
			
	unset($_SESSION['ppData']);
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data inserted Complete.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on data insearting. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
