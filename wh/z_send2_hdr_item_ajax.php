<?php
include 'session.php';	
//include 'inc_helper.php';	
	
try{	
    $sdNo = $_POST['sdNo'];
	
	$pdo->beginTransaction();	
	
	if(!empty($_POST['itmId']) and isset($_POST['itmId']))
    {
		//$arrProdItems=explode(',', $prodItems);
        foreach($_POST['itmId'] as $index => $item )
        {	
			$sql = "INSERT INTO `send_detail`
			(`refNo`, `prodItemId`, `sdNo`)
			SELECT dtl.sendId, dtl.productItemId, :sdNo 
			FROM send_detail_mssql dtl 
			WHERE dtl.sendId=:sendId 
			AND dtl.productItemId=:productItemId 
			";			
			$arrItm=explode(',', $item)
			$stmt = $pdo->prepare($sql);			
			$stmt->bindParam(':sendId', $arrItm[0]);	
			$stmt->bindParam(':productItemId', $arrItm[1]);		
			$stmt->bindParam(':sdNo', $sdNo);		
			$stmt->execute();			
        }
    }
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'sdNo' => $sdNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Update. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors.$t));
}


