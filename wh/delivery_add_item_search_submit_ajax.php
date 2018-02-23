<?php
include 'session.php';	
include '../db/database_sqlsrv.php';
include 'inc_helper.php';	
	
try{
	$s_userID = $_SESSION['userID']; 
	
	$doNo = $_POST['doNo'];
    $prodCode = $_POST['prodCode'];	
	
	$pdo->beginTransaction();
	
	
	if(!empty($_POST['prodItemId']) and isset($_POST['prodItemId']))
    {
		//$arrProdItems=explode(',', $prodItems);
        foreach($_POST['prodItemId'] as $item)
        {				
			$sql = "INSERT INTO `delivery_detail` 
			(`doNo`, `prodCode`, `prodItemId`) 
			VALUES
			(:doNo, :prodCode,:prodItemId)";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':doNo', $doNo);	
			$stmt->bindParam(':prodCode', $prodCode);	
			$stmt->bindParam(':prodItemId', $item);	
			$stmt->execute();
        }
    }
		
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'doNo' => $doNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors.$t));
}


