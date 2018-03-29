<?php
include 'session.php';	
//include 'inc_helper.php';	
	
try{
	$t = "";
	$s_userId = $_SESSION['userId']; 
	
    $sdNo = $_POST['sdNo'];
	
	$pdo->beginTransaction();
	
	if(!empty($_POST['prodItemId']) and isset($_POST['prodItemId']))
    {
		//$arrProdItems=explode(',', $prodItems);
        foreach($_POST['prodItemId'] as $index => $item )
        {	
			$sql = "INSERT INTO `send_detail`
			(`prodItemId`, `sdNo`)
			SELECT rc.`prodItemId`, :sdNo 
			FROM receive_detail rc 
			WHERE rc.id=:id 
			";						
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':sdNo', $sdNo);	
			$stmt->bindParam(':id', $item);		
			$stmt->execute();			
        }
    }
	
	
	
		
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'rtNo' => $rtNo));
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


