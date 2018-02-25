<?php
include 'session.php';	
	
try{
	
	$s_userId = $_SESSION['userId']; 
	$doNo = $_POST['doNo']; 
	
	$pdo->beginTransaction();
	
	if(!empty($_POST['id']) and isset($_POST['id']))
    {
		//$arrProdItems=explode(',', $prodItems);
        foreach($_POST['id'] as $index => $item )
        {	
			$sql = "UPDATE `delivery_prod` SET remark=:remark WHERE id=:id 
			";						
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':remark', $_POST['remark'][$index]);	
			$stmt->bindParam(':id', $item);		
			$stmt->execute();			
        }
    }
	
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Submit Complete.', 'doNo' => $doNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Submit. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


