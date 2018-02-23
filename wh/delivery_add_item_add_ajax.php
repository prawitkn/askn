<?php
include 'session.php';	
	
try{
	
	$s_userId = $_SESSION['userId']; 
	
    $doNo = $_POST['doNo'];
	//$id = $_POST['id'];
	//$remark = $_POST['remark'];
	
	$pdo->beginTransaction();
	
	/*$sql = "SELECT id FROM delivery_detail dd 
			WHERE 1
			AND dd.doNo=:doNo AND dd.prodCode=:prodCode LIMIT 1
			";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':doNo', $doNo);
	$stmt->bindParam(':prodCode', $prodCode);
	$stmt->execute();
	
	$pdo->beginTransaction();*/
	
	
	if(!empty($_POST['id']) and isset($_POST['remark']))
    {
		//$arrProdItems=explode(',', $prodItems);
        foreach($_POST['id'] as $id)
        {					
			$sql = "UPDATE `delivery_detail` dd SET dd.remark=:remark WHERE dd.id=:id ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':remark', $remark);	
			$stmt->bindParam(':id', $id);	
			$stmt->execute();
			
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
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}


