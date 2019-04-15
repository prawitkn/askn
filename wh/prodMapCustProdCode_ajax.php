<?php
    include 'session.php';	
		
	$tb='wh_product_code_by_customer';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				$prodId = $_POST['prodId'];
				$custId = $_POST['custId'];
				$custProdCode = $_POST['custProdCode'];
				$custProdDesc = $_POST['custProdDesc'];
								
				// Check duplication?
				$sql = "SELECT id FROM `".$tb."` WHERE custId=:custId AND prodId=:prodId ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':custId', $custId);
				$stmt->bindParam(':prodId', $prodId);
				$stmt->execute();
				if ($stmt->rowCount() >= 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Insertion. Duplicate data, Please try new product and customer. " . $pdo->errorInfo()[2];
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				}   
	
				$sql = "INSERT INTO `".$tb."` (`custId`, `prodId`, `prodCode`, `prodDesc`, `statusCode`, `createTime`, `createById`)
				 VALUES (:custId,:prodId,:prodCode,:prodDesc,'A',NOW(),:s_userId)";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':custId', $custId);
				$stmt->bindParam(':prodId', $prodId);
				$stmt->bindParam(':prodCode', $custProdCode);
				$stmt->bindParam(':prodDesc', $custProdDesc);
				$stmt->bindParam(':s_userId', $s_userId);
				if ($stmt->execute()) {
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
				} else {
					header('Content-Type: application/json');
					$errors = "Error on Data Insertion. " . mysqli_error($link);
					echo json_encode(array('success' => false, 'message' => $errors));
				}				
				break;
				exit();

			case 'edit' : 
				$id = $_POST['id'];
				$prodId = $_POST['prodId'];
				$custId = $_POST['custId'];
				$custProdCode = $_POST['custProdCode'];
				$custProdDesc = $_POST['custProdDesc'];
				$statusCode = $_POST['statusCode'];
				
				// Check user name duplication?
				$sql = "SELECT id FROM `".$tb."` WHERE (prodId=:prodId AND custId=:custId) AND id<>:id ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':prodId', $prodId);
				$stmt->bindParam(':custId', $custId);
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				if ($stmt->rowCount() >= 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Insertion. Duplicate data." . $pdo->errorInfo()[2];
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				} 	   
				
				//Sql
				$sql = "UPDATE `".$tb."` SET `prodId`=:prodId 
				, `custId`=:custId, `prodCode`=:prodCode, `prodDesc`=:prodDesc
				, `statusCode`=:statusCode
				WHERE id=:id 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':prodId', $prodId);
				$stmt->bindParam(':custId', $custId);
				$stmt->bindParam(':prodCode', $custProdCode);
				$stmt->bindParam(':prodDesc', $custProdDesc);
				$stmt->bindParam(':statusCode', $statusCode);
				$stmt->bindParam(':id', $id);
				if ($stmt->execute()) {
					  header('Content-Type: application/json');
					  echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
				   } else {
					  header('Content-Type: application/json');
					  $errors = "Error on Data Update. Please try new data. " . $pdo->errorInfo();
					  echo json_encode(array('success' => false, 'message' => $errors));
				}	
				break;
			case 'setActive' :
				$id = $_POST['id'];
				$statusCode = $_POST['statusCode'];	
				
				$sql = "UPDATE ".$tb." SET statusCode=:statusCode WHERE id=:id ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':statusCode', $statusCode);
				$stmt->bindParam(':id', $id);
				$stmt->execute();	
				if ($stmt->execute()) {
				  header('Content-Type: application/json');
				  echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
				} else {
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new data. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));
				}	
				break;
			case 'remove' :
				$id = $_POST['id'];
				
				$sql = "UPDATE ".$tb." SET statusCode='X' WHERE id=:id ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':id', $id);
				$stmt->execute();	
				if ($stmt->execute()) {
				  header('Content-Type: application/json');
				  echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
				} else {
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new data. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));
				}	
				break;
			case 'delete' :
				$id = $_POST['id'];
				
				$sql = "DELETE FROM ".$tb." WHERE id=:id ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':id', $id);
				$stmt->execute();	
				if ($stmt->execute()) {
				  header('Content-Type: application/json');
				  echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
				} else {
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new data. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));
				}	
				break;
			default : 
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
		}
	}