<?php
    include 'session.php';	
	
	$tb='wh_pick_cond';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :		
				$custId = $_POST['custId'];
				$prodId = $_POST['prodId'];
				$maxDays = $_POST['maxDays'];
								
				// Check duplication?
				$sql = "SELECT id FROM `".$tb."` WHERE custId=:custId OR prodId=:prodId ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':custId', $custId);
				$stmt->bindParam(':prodId', $prodId);
				$stmt->execute();
				if ($stmt->rowCount() >= 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Insertion. Duplicate data, Please try new data. " . $pdo->errorInfo()[2];
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				}   
	
				$sql = "INSERT INTO `".$tb."` (`custId`,`prodId`,`maxDays`, `statusCode`)
				 VALUES (:custId,:prodId,:maxDays,'A')";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':custId', $custId);				
				$stmt->bindParam(':prodId', $prodId);
				$stmt->bindParam(':maxDays', $maxDays);
				if ($stmt->execute()) {
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
				} else {
					header('Content-Type: application/json');
					$errors = "Error on Data Insertion. Please try new username. " . mysqli_error($link);
					echo json_encode(array('success' => false, 'message' => $errors));
				}				
				break;
				exit();
			case 'edit' :
				$id = $_POST['id'];
				//$custId = $_POST['custId'];
				//$prodId = $_POST['prodId'];
				$maxDays = $_POST['maxDays'];
				$statusCode = $_POST['statusCode'];	
								
				//Sql
				$sql = "UPDATE `".$tb."` SET `maxDays`=:maxDays 
				, `statusCode`=:statusCode 
				WHERE id=:id 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':maxDays', $maxDays);
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