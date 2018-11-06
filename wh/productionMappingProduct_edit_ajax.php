<?php

    include 'session.php';	
	
    $newWmsProdId = $_POST['wmsProdId'];
	$statusCode = $_POST['statusCode'];
    $id = $_POST['id'];    
	
	try{
 // Check user name duplication?
    $sql = "SELECT statusCode FROM product_mapping WHERE invProdId=:id ";
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':id', $id);	
	$stmt->execute();	
	
    if ($stmt->rowCount() < 1){
      header('Content-Type: application/json');
      $errors = "Error on Data Update. Data not found. " . $pdo->errorInfo();
      echo json_encode(array('success' => false, 'message' => $errors));  
      exit;    
    } 
	
	//We start our transaction.
	$pdo->beginTransaction();
	
	//get mapping table 
	$sql = "SELECT  `invProdId`, `invProdName`, `wmsProdId`, `statusCode` 
	FROM `product_mapping` 
	WHERE invProdId=:id 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':id', $id);	
	$stmt->execute();
	$row=$stmt->fetch();

	$invProdId=$row['invProdId'];
	$oldWmsProdId=$row['wmsProdId'];

	//update mapping table 
	$sql = "UPDATE `product_mapping` SET `wmsProdId`=:newWmsProdId 
	, `statusCode`=:statusCode
	WHERE invProdId=:id 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':newWmsProdId', $newWmsProdId);
	$stmt->bindParam(':statusCode', $statusCode);
	$stmt->bindParam(':id', $id);	
	$stmt->execute();

	//Auto mapping product_item 
	$sql = "UPDATE product_item itm 
	SET itm.prodCodeId=:newWmsProdId 
	WHERE itm.prodId=:id 
	AND (itm.prodCodeId = 0 OR itm.prodCodeId IS NULL) 
	";	
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':newWmsProdId', $newWmsProdId);
	$stmt->bindParam(':id', $id);	
	$stmt->execute();
	
	if ( $oldWmsProdId <> $newWmsProdId ) {
		//Auto mapping product_item 
		$sql = "SELECT `prodId`, `sloc`, `open`, `onway`, `receive`, `send`, `sales`, `delivery`, `balance` FROM stk_bal WHERE prodId=:oldWmsProdId 	
		";	
		$stmt = $pdo->prepare($sql);	
		$stmt->bindParam(':oldWmsProdId', $oldWmsProdId);
		$stmt->execute();
		while ( $row = $stmt->fetch() ){	
			$sloc = $row['sloc'];

			$sql = "SELECT `prodId`, `sloc`, `open`, `onway`, `receive`, `send`, `sales`, `delivery`, `balance` FROM stk_bal 
			WHERE prodId=:newWmsProdId AND sloc=:sloc 
			";	
			$stmt2 = $pdo->prepare($sql);	
			$stmt2->bindParam(':newWmsProdId', $newWmsProdId);
			$stmt2->bindParam(':sloc', $sloc );
			$stmt2->execute();		
			if( $stmt2->rowCount() > 0 ){
				$row2 = $stmt2->fetch();

				$sql = "UPDATE stk_bal SET open=open+:open, onway=onway+:onway, receive=receive+:receive, send=send+:send, sales=sales+:sales, delivery=delivery+:delivery, balance=balance+:balance 
				WHERE prodId=:newWmsProdId AND sloc=:sloc 
				";	
				$stmt3 = $pdo->prepare($sql);	
				$stmt3->bindParam(':newWmsProdId', $newWmsProdId);
				$stmt3->bindParam(':sloc', $sloc );

				$stmt3->bindParam(':open', $row2['open']);
				$stmt3->bindParam(':onway', $row2['onway']);
				$stmt3->bindParam(':receive', $row2['receive']);
				$stmt3->bindParam(':send', $row2['send']);
				$stmt3->bindParam(':sales', $row2['sales']);			
				$stmt3->bindParam(':delivery', $row2['delivery']);
				$stmt3->bindParam(':balance', $row2['balance']);
				$stmt3->execute();										
			}else{
				//Just update old prod id > new prod id 
				$sql = "UPDATE stk_bal itm 
				SET itm.prodId=:newWmsProdId 
				WHERE itm.prodId=:oldWmsProdId 
				AND itm.sloc=:sloc 
				";	
				$stmt3 = $pdo->prepare($sql);	
				$stmt3->bindParam(':newWmsProdId', $newWmsProdId);
				$stmt3->bindParam(':oldWmsProdId', $oldWmsProdId);
				$stmt3->bindParam(':sloc', $sloc );
				$stmt3->execute();			
			} //if rowCount by oldProdId > 0 

		} //foreach old wms prod id loop. 
		
		//delete old wms prod id 
		$sql = "DELETE FROM stk_bal WHERE prodId=:oldWmsProdId 
		";	
		$stmt3 = $pdo->prepare($sql);	
		$stmt3->bindParam(':oldWmsProdId', $oldWmsProdId);	
		$stmt3->execute();	

	}	// change prod id 

	//We've got this far without an exception, so commit the changes.
	$pdo->commit();
	
	//return JSON	
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
	} 
	//Our catch block will handle any exceptions that are thrown.
	catch(Exception $e){
		//Rollback the transaction.
		$pdo->rollBack();
		//return JSON
		header('Content-Type: application/json');
		$errors = "Error on Data Update. " . $e->getMessage();
		echo json_encode(array('success' => false, 'message' => $errors));
	}  
