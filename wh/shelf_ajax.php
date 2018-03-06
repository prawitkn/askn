<?php
    include 'session.php';	
	
function to_thai_date($eng_date){
	if(strlen($eng_date) != 10){
		return null;
	}else{
		$new_date = explode('-', $eng_date);

		$new_y = (int) $new_date[0] + 543;
		$new_m = $new_date[1];
		$new_d = $new_date[2];

		$thai_date = $new_d . '/' . $new_m . '/' . $new_y;

		return $thai_date;
	}
}
function to_thai_datetime_fdt($eng_date){
	//if(strlen($eng_date) != 10){
	//    return null;
	//}else{
		$new_datetime = explode(' ', $eng_date);
		$new_date = explode('-', $new_datetime[0]);

		$new_y = (int) $new_date[0] + 543;
		$new_m = $new_date[1];
		$new_d = $new_date[2];

		$thai_date = $new_d . '/' . $new_m . '/' . $new_y . ' ' . substr($new_datetime[1],0,5);

		return $thai_date;
	//}
}
function to_mysql_date($thai_date){
	if(strlen($thai_date) != 10){
		return null;
	}else{
		$new_date = explode('/', $thai_date);

		$new_y = (int)$new_date[2] - 543;
		$new_m = $new_date[1];
		$new_d = $new_date[0];

		$mysql_date = $new_y . '-' . $new_m . '-' . $new_d;

		return $mysql_date;
	}
}
	
	$tb='wh_shelf';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :		
				$xId = $_POST['xId'];
				$yId = $_POST['yId'];
				$zId = $_POST['zId'];
				$code = $_POST['hidCode'];
				$name = $_POST['name'];
								
				// Check duplication?
				$sql = "SELECT id FROM `".$tb."` WHERE code=:code OR name=:name ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
				$stmt->execute();
				if ($stmt->rowCount() >= 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Insertion. Duplicate data, Please try new username. " . $pdo->errorInfo()[2];
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				}   
	
				$sql = "INSERT INTO `".$tb."` (`xId`,`yId`,`zId`, `code`, `name`, `statusCode`)
				 VALUES (:xId,:yId,:zId,:code,:name,'A')";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':xId', $xId);				
				$stmt->bindParam(':yId', $yId);
				$stmt->bindParam(':zId', $zId);
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
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
				$xId = $_POST['xId'];
				$yId = $_POST['yId'];
				$zId = $_POST['zId'];
				$code = $_POST['hidCode'];
				$name = $_POST['name'];
				
				// Check user name duplication?
				$sql = "SELECT id FROM `".$tb."` WHERE code=:code OR name=:name ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
				$stmt->execute();
				if ($stmt->rowCount() <> 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new username. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				}			  
				
				//Sql
				$sql = "UPDATE `".$tb."` SET `code`=:code 
				, `name`=:name
				, `xId`=:xId
				, `yId`=:yId
				, `zId`=:zId
				WHERE id=:id 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);	
				$stmt->bindParam(':xId', $xId);
				$stmt->bindParam(':yId', $yId);
				$stmt->bindParam(':zId', $zId);
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
				$statusCode = $_POST['statusCode'];	
				
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