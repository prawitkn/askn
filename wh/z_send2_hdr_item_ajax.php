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
<<<<<<< HEAD:wh/send2_hdr_item_ajax.php
			SELECT :refProdSdNo, rc.`prodItemId`, :sdNo 
			FROM send_prod_detail rc 
			WHERE rc.id=:id 
			";			
			$arrItm=explode(',', $item)
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $arrItm[0]);		
			$stmt->bindParam(':refProdSdNo', $arrItm[1]);	
			$stmt->bindParam(':id', $item);		
=======
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
>>>>>>> 7ea32d11a154a011105226a8f4e310d4e4756f4a:wh/z_send2_hdr_item_ajax.php
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


