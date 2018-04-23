<?php
include 'session.php';	
	
try{
	
	$s_userID = $_SESSION['userID']; 
	
	$rowCount =  count($_POST['code']); 
	
	//$qty = $_POST['qty'];
	$pdo->beginTransaction();
	
    for($i=1; $i<=$rowCount; $i++)
	{
		$sql = "UPDATE customer SET `code`=:newCode, `custName`=:custName 
		,`locationCode`=:locationCode,`marketCode`=:marketCode,`custAddr`=:custAddr 
		,`custContact`=:custContact,`custContactPosition`=:custContactPosition,`zipcode`=:zipcode 
		,`taxId`=:taxId,`accNo`=:accNo,`creditDay`=:creditDay 
		,`creditLimit`=:creditLimit,`accCond`=:accCond,`custEmail`=:custEmail 
		,`custTel`=:custTel,`custFax`=:custFax,`smCode`=:smCode 
		,`smAdmCode`=:smAdmCode  
		WHERE `code`=:code 
		";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':newCode', $_POST['newCode'][$i -1]);	
		$stmt->bindParam(':custName', $_POST['custName'][$i -1]);	
		$stmt->bindParam(':locationCode', $_POST['locationCode'][$i -1]);
		$stmt->bindParam(':marketCode', $_POST['marketCode'][$i -1]);
		$stmt->bindParam(':custAddr', $_POST['custAddr'][$i -1]);
		$stmt->bindParam(':custContact', $_POST['custContact'][$i -1]);
		$stmt->bindParam(':custContactPosition', $_POST['custContactPosition'][$i -1]);
		$stmt->bindParam(':zipcode', $_POST['zipcode'][$i -1]);
		$stmt->bindParam(':taxId', $_POST['taxId'][$i -1]);
		$stmt->bindParam(':accNo', $_POST['accNo'][$i -1]);
		$stmt->bindParam(':creditDay', $_POST['creditDay'][$i -1]);
		$stmt->bindParam(':creditLimit', $_POST['creditLimit'][$i -1]);
		$stmt->bindParam(':accCond', $_POST['accCond'][$i -1]);
		$stmt->bindParam(':custEmail', $_POST['custEmail'][$i -1]);
		$stmt->bindParam(':custTel', $_POST['custTel'][$i -1]);
		$stmt->bindParam(':custFax', $_POST['custFax'][$i -1]);
		$stmt->bindParam(':smCode', $_POST['smCode'][$i -1]);
		$stmt->bindParam(':smAdmCode', $_POST['smAdmCode'][$i -1]);
		$stmt->bindParam(':smCode', $_POST['smCode'][$i -1]);
		$stmt->bindParam(':smCode', $_POST['smCode'][$i -1]);		
		$stmt->bindParam(':code', $_POST['code'][$i -1]);
		$stmt->execute();	
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


