<?php
include 'session.php';

//We will need to wrap our queries inside a TRY / CATCH block.
//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
try{	
	//$session_userID=$_SESSION['userID'];
	
	$soNo = $_POST['soNo'];
	//$hdrTotal = $_POST['hdrTotal'];
	//$hdrVatAmount = $_POST['hdrVatAmount'];
	//$hdrNetTotal = $_POST['hdrNetTotal'];

	//We start our transaction.
	$pdo->beginTransaction();	
	
	//Query 1: Check Status for not gen running No.
	$sql = "SELECT * FROM sale_header WHERE soNo=:soNo AND statusCode='B' LIMIT 1";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':soNo', $soNo);
	$stmt->execute();
	$row_count = $stmt->rowCount();	
	if($row_count != 1){
		//return JSON
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
		exit();
	}
	
	//Query 2: UPDATE DATA
	/*$sql = "UPDATE sale_header SET statusCode='C'   
		, total=? 
		, vatAmount=? 
		, netTotal=? 
		, confirmTime=now()
		, confirmById=?
		WHERE soNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
            $hdrTotal, 
            $hdrVatAmount,
			$hdrNetTotal,	
			$s_userID,
			$soNo	
        )
    );*/
	$sql = "UPDATE sale_header SET statusCode='C'   
		, confirmTime=now()
		, confirmById=?
		WHERE soNo=? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
			$s_userId,
			$soNo	
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
	$errors = "Error on data confirmation. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
?>     

