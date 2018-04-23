<?php
include 'session.php'; /*$s_userID=$_SESSION['userID'];
		$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	
	//$session_userID=$_SESSION['userID'];
	
	$docNo = $_POST['docNo'];

	//We start our transaction.
	$pdo->beginTransaction();	
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT docNo FROM inv_ret WHERE docNo=:docNo AND statusCode='B' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':docNo', $docNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 2: UPDATE DATA
	$sql = "UPDATE inv_ret SET statusCode='C'   
		, confirmTime=now()
		, confirmById=?
		WHERE docNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(	
			$s_userID,
			$docNo	
        )
    );
	    
    //We've got this far without an exception, so commit the changes.
    $pdo->commit();
	
    //return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data Confirmed'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Confirmation. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

