<?php
include 'session.php';	 /*$s_userID=$_SESSION['userID'];
		$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
include 'inc_helper.php';	
	
try{	
    $docNo = $_POST['docNo'];
	
	$pdo->beginTransaction();
	
	if(!empty($_POST['id']) and isset($_POST['id']))
    {
		//$arrProdItems=explode(',', $prodItems);
        foreach($_POST['id'] as $index => $item )
        {	
			$sql = "INSERT INTO `inv_ret_detail` 
			(`prodItemId`, `returnReasonCode`, `returnReasonRemark`, `docNo`)
			VALUES 
			(:prodItemId, :returnReasonCode, :returnReasonRemark, :docNo) 
			";						
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':prodItemId', $_POST['prodItemId'][$index]);	
			$stmt->bindParam(':returnReasonCode', $_POST['returnReasonCode'][$index]);	
			$stmt->bindParam(':returnReasonRemark', $_POST['returnReasonRemark'][$index]);	
			$stmt->bindParam(':docNo', $docNo);	
			$stmt->execute();			
        }
    }
	
	
	
		
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'docNo' => $docNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
    //Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Inserted. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors.$t));
}


