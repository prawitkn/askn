<?php
include 'inc_helper.php';  
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode
	
try{   	
	$invNo = $_POST['invNo'];
	$rowCount =  count($_POST['id']); 
	
	//$qty = $_POST['qty'];
	$pdo->beginTransaction();
	
	$totalExcVat = 0.00;
	$vatAmount = 0.00;
	$totalIncVat = 0.00;
    for($i=1; $i<=$rowCount; $i++)
	{
		$id = $_POST['id'][$i -1];
		$salesPrice = str_replace( ',', '', $_POST['salesPrice'][$i -1] );
		$total = ($_POST['qty'][$i -1]*$salesPrice);
		$netTotal = $total;		
		
		$sql = "UPDATE `invoice_detail` SET salesPrice=:salesPrice
		, total=:total, netTotal=:netTotal
		WHERE 1
		AND id=:id 
		";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':salesPrice', $salesPrice);	
		$stmt->bindParam(':total', $total);	
		$stmt->bindParam(':netTotal', $netTotal);		
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$totalExcVat += $netTotal;
	}
	
	$vatAmount = $totalExcVat * 0.07;
	$totalIncVat = $totalExcVat + $vatAmount;
	
	$sql = "UPDATE `invoice_header` SET statusCode='B'
	, totalExcVat=:totalExcVat
	, vatAmount=:vatAmount, totalIncVat=:totalIncVat
	WHERE 1
	AND invNo=:invNo 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':totalExcVat', $totalExcVat);	
	$stmt->bindParam(':vatAmount', $vatAmount);	
	$stmt->bindParam(':totalIncVat', $totalIncVat);		
	$stmt->bindParam(':invNo', $invNo);
	$stmt->execute();	
	
			
	$pdo->commit();
	
	header('Content-Type: application/json');
    echo json_encode(array('success' => true, 'message' => 'Data Calculate Complete.', 'invNo' => $invNo));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Calculate. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}
