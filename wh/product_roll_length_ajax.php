<?php
    include 'session.php';	
		
	$tb='product_roll_length';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				//$id = $_POST['id'];
				$prodId = $_POST['prodId'];
				$name = $_POST['name'];
								
				//Check Duplicate shipto
				 $sql = "SELECT * FROM `".$tb."` WHERE prodId=:prodId AND `name`=:name LIMIT 1 "; 
				 $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':prodId', $prodId); $stmt->bindParam(':name', $name); 
				$stmt->execute();
				if($stmt->rowCount()>=1){
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Duplicate data.'));
					exit;
				}		
				//INsert customer
				$sql = "INSERT INTO `".$tb."`(`prodId`, `name`
				, `statusCode`, `createTime`, `createById`) 
				 VALUES 
				(:prodId,:name
				,'A', now(), :s_userId)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':prodId', $prodId); $stmt->bindParam(':name', $name);
				$stmt->bindParam(':s_userId', $s_userId);

				if ($stmt->execute()) {
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
				} else {
					header('Content-Type: application/json');
					$errors = "Error on Data Insertion. Please try new username. " . $pdo->errorInfo();
					echo json_encode(array('success' => false, 'message' => $errors));
				}				
				break;
				exit();
			case 'edit' :
				$id = $_POST['id'];
				$prodId = $_POST['prodId'];
				$name = $_POST['name'];
				$statusCode = (isset($_POST['statusCode'])? $_POST['statusCode'] : 'I' );
								
				$sql = "UPDATE `".$tb."` SET `prodId`=:prodId, `name`=:name
				, `statusCode`=:statusCode 				
				WHERE id=:id 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':prodId', $prodId); 
				$stmt->bindParam(':name', $name); 
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
				
				$sql = "UPDATE `".$tb."` SET statusCode=:statusCode WHERE id=:id ";
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
				
				$sql = "UPDATE `".$tb."` SET statusCode='X' WHERE id=:id ";
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
				
				$sql = "DELETE FROM `".$tb."` WHERE id=:id ";
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