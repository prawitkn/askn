<?php
include 'session.php';

//Check user roll.
switch($s_userGroupCode){
	case 'it' : case 'admin' : case 'whAdmin' : 
		break;
	default : 
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
		exit();
}

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	
	//$session_userID=$_SESSION['userID'];
	
	$rcNo = $_POST['rcNo'];

	//We start our transaction.
	$pdo->beginTransaction();	
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM receive WHERE rcNo=:rcNo AND statusCode='B' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':rcNo', $rcNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 2: UPDATE DATA
	$sql = "UPDATE receive SET statusCode='C'   
		, confirmTime=now()
		, confirmById=?
		WHERE rcNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(	
			$s_userID,
			$rcNo	
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

