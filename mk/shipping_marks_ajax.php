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
	
	$tb='shipping_marks';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				//$id = $_POST['id'];
				$code = trim($_POST['code']);
				$typeCode = $_POST['typeCode'];
				$name = trim($_POST['name']);
				$statusCode = (isset($_POST['statusCode'])? 'A' : 'I' );
				
				$curPhoto = $_POST['curPhoto'];
					
				$new_picture_name=$curPhoto;
				 
				 //Check Duplicate
				 $sql = "SELECT * FROM `product` WHERE `code`=:code LIMIT 1 ";     
				 $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':code', $code); 
				$stmt->execute();
				if($stmt->rowCount()>=1){
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Duplicate data.'));
					exit;
				}	
				
				if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
					// If the old picture already exists, delete it
					//if (file_exists('../images/shippingMarks/'.$curPhoto)) unlink('../images/shippingMarks/'.$curPhoto);
				
					$new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "../images/shippingMarks/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}
					
				$sql = "INSERT INTO `shipping_marks`(`code`, `name`, `typeCode`, `filePath`
				, `statusCode`, `createTime`, `createById`)  
				VALUES (	
				:code,:name,:typeCode,:photo
				,'A', now(), :s_userId  
				)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':code', $code); 
				$stmt->bindParam(':typeCode', $typeCode); 
				$stmt->bindParam(':name', $name); 
				$stmt->bindParam(':photo', $new_picture_name); 
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
				try{	
					$id = $_POST['id'];
					$code = $_POST['code'];
					$typeCode = $_POST['typeCode'];
					$name = $_POST['name'];
					$statusCode = (isset($_POST['statusCode'])? 'A' : 'I' );
					
					
					$curPhoto = $_POST['curPhoto'];
						
					$new_picture_name=$curPhoto;
						
					 // Upload Picture
					if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
						// If the old picture already exists, delete it
						if (file_exists('../images/shippingMarks/'.$curPhoto)) unlink('../images/shippingMarks/'.$curPhoto);
					
						$new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
						$path_upload = "../images/shippingMarks/".$new_picture_name;
						move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
					}
					
					$sql = "UPDATE `shipping_marks` SET `code`=:code
					, `typeCode`=:typeCode
					, `name`=:name
					, `filePath`=:new_picture_name
					, `statusCode`=:statusCode
					WHERE id=:id
					"; 
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':code', $code);
					$stmt->bindParam(':typeCode', $typeCode);
					$stmt->bindParam(':name', $name);
					$stmt->bindParam(':new_picture_name', $new_picture_name);
					$stmt->bindParam(':statusCode', $statusCode);
					$stmt->bindParam(':id', $id);
					$stmt->execute();
					
					header('Content-Type: application/json');
					  echo json_encode(array('success' => true, 'message' => 'Data Update Complete.'));
				}catch(Exception $e){
					header('Content-Type: application/json');
				  $errors = "Error on Data Verify. Please try again. " . $e->getMessage();
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
				try{
					$id = $_POST['id'];
					
					$pdo->beginTransaction();
					
					//delete image
					$sql = "SELECT filePath FROM shipping_marks WHERE id=:id ";
					$result_img = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();
					$row = $stmt->fetch();
					
					if($row['filePath']<>""){
						@unlink('../images/shippingMarks/'.$row['filePath']);
					}

					$sql = "DELETE FROM shipping_marks WHERE id=:id ";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();

					$pdo->commit();	
					
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data deleted'));
					
				}catch(Exception $e){
					//Rollback the transaction.
					$pdo->rollBack();
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;
			default : 
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
		}
	}