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
	
	$tb='salesman';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				//hdr.`id`, hdr.`code`, hdr.`name`, hdr.`surname`, hdr.`smType`, hdr.`photo`, hdr.`positionName`, hdr.`mobileNo`, hdr.`email`, hdr.`statusCode`
				//$id = $_POST['id'];
				$code = trim($_POST['code']);
				$name = trim($_POST['name']);
				$surname = trim($_POST['surname']);
				$smType = $_POST['smType'];
				//$photo = $_POST['photo'];
				$positionName = trim($_POST['positionName']);
				$mobileNo = trim($_POST['mobileNo']);
				$email = trim($_POST['email']);
				$statusCode = $_POST['statusCode'];
				
				//Check user name duplication?
				$sql = "SELECT hdr.`id` FROM ".$tb." hdr WHERE hdr.code=:code ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':code', $code);	
				$stmt->execute();					
				if($stmt->rowCount()>=1){
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Duplicate Code.'));
					exit;
				}	 		
				
				$new_picture_name="";
				 // Upload Picture
				if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
					$new_picture_name = 'sm_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "dist/img/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}								
				
				//INsert 
				$sql = "INSERT INTO `".$tb."`(`code`, `name`, `surname`, `smType`, `photo`, `positionName`, `mobileNo`, `email`, `statusCode`)
				 VALUES 
				(:code,:name,:surname,:smType,:new_picture_name,:positionName,:mobileNo,:email,:statusCode)";
				$stmt = $pdo->prepare($sql);
				
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
				$stmt->bindParam(':surname', $surname);
				$stmt->bindParam(':smType', $smType);
				$stmt->bindParam(':new_picture_name', $new_picture_name);
				$stmt->bindParam(':positionName', $positionName);
				$stmt->bindParam(':mobileNo', $mobileNo);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':statusCode', $statusCode);
				//$stmt->bindParam(':id', $id);
				
				if ($stmt->execute()) {
					  header('Content-Type: application/json');
					  echo json_encode(array('success' => true, 'message' => 'Complete.'));
				   } else {
					  header('Content-Type: application/json');
					  $errors = "Error on Data Insert. Please try new data. " . $pdo->errorInfo();
					  echo json_encode(array('success' => false, 'message' => $errors));
				}				
				break;
				exit();
			case 'edit' :
				//hdr.`id`, hdr.`code`, hdr.`name`, hdr.`surname`, hdr.`smType`, hdr.`photo`, hdr.`positionName`, hdr.`mobileNo`, hdr.`email`, hdr.`statusCode`
				$id = $_POST['id'];
				$code = trim($_POST['code']);
				$name = trim($_POST['name']);
				$surname = trim($_POST['surname']);
				$smType = $_POST['smType'];
				//$photo = $_POST['photo'];
				$positionName = trim($_POST['positionName']);
				$mobileNo = trim($_POST['mobileNo']);
				$email = trim($_POST['email']);
				$statusCode = $_POST['statusCode'];
				
				$curPhoto = $_POST['curPhoto'];
				$new_picture_name=$curPhoto;
				
					 				
				
				//Check exists?
				$sql = "SELECT hdr.`id`, hdr.`code`, hdr.`name`, hdr.`surname`,hdr.`photo` FROM ".$tb." hdr WHERE hdr.id=$id LIMIT 1 ";				
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();	
				if ($stmt->rowCount() <> 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new data. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				}
				
				//Check Duplicat code with another 
				$row=$stmt->fetch();
				if($code<>$row['code']){
					//Check user name duplication?
					$sql = "SELECT hdr.`id` FROM ".$tb." hdr WHERE hdr.code=:code LIMIT 1 ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':code', $code);	
					$stmt->execute();					
					if($stmt->rowCount()==1){
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Duplicate Code.'));
						exit;
					}
				}
							  
				//inputFile
				if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
					// If the old picture already exists, delete it
					if (file_exists('dist/img/'.$curPhoto)) unlink('dist/img/'.$curPhoto);
				
					$new_picture_name = 'sm_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "dist/img/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}				
				//hdr.`id`, hdr.`code`, hdr.`name`, hdr.`surname`, hdr.`smType`, hdr.`photo`, hdr.`positionName`, hdr.`mobileNo`, hdr.`email`, hdr.`statusCode`
				$sql = "UPDATE `".$tb."` SET `code`=:code 
				, `name`=:name
				, `surname`=:surname
				, `smType`=:smType 
				, `photo`=:new_picture_name
				, `positionName`=:positionName
				, `mobileNo`=:mobileNo
				, `email`=:email
				, `statusCode`=:statusCode 
				WHERE id=:id 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
				$stmt->bindParam(':surname', $surname);
				$stmt->bindParam(':smType', $smType);
				$stmt->bindParam(':new_picture_name', $new_picture_name);
				$stmt->bindParam(':positionName', $positionName);
				$stmt->bindParam(':mobileNo', $mobileNo);
				$stmt->bindParam(':email', $email);
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
				exit();
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
				exit();
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
				exit();
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
				exit();
			default : 
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
		}
	}