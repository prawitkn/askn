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
	
	$tb='user';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				$userFullname = $_POST['userFullname'];
				$userName = $_POST['userName'];
				$userPassword = mysqli_real_escape_string($link, $_POST['userPassword']);
				$userEmail = $_POST['userEmail'];
				$userTel = $_POST['userTel'];
				$userGroupCode = $_POST['userGroupCode'];
				$smId = $_POST['smId'];
				
			 // Check user name duplication?
				$sql_user = "SELECT userName FROM user WHERE userName='$userName'";
				$result_user = mysqli_query($link, $sql_user);
				$is_user = mysqli_num_rows($result_user);
				if ($is_user >= 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Insertion. Duplicate data, Please try new username. " . mysqli_error($link);
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				}   
				
			 // Encript Password
				$salt = "asdadasgfd";
				$hash_userPassword = hash_hmac('sha256', $userPassword, $salt);

				$new_picture_name="";
				 // Upload Picture
				if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
					$new_picture_name = 'user_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "dist/img/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}
				
				
				$sql = "INSERT INTO `user` (`userName`, `userPassword`, `userFullname`, `userEmail`, `userTel`, `userPicture`, `userGroupCode`,  `smId`, `statusCode`)"
						. " VALUES ('$userName', '$hash_userPassword', '$userFullname', '$userEmail', '$userTel', '$new_picture_name', '$userGroupCode', $smId,'A')";
			 
				$result = mysqli_query($link, $sql);
			 
				if ($result) {
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
				$userId = $_POST['userId'];
				$userFullname = $_POST['userFullname'];
				$userName = $_POST['userName'];
				$userPassword = $_POST['userPassword'];
				$userEmail = $_POST['userEmail'];
				$userTel = $_POST['userTel'];
				$userGroupCode = $_POST['userGroupCode'];
				$smId = $_POST['smId'];
				$statusCode = $_POST['statusCode'];
				
				$curPhoto = $_POST['curPhoto'];
				$new_picture_name=$curPhoto;
				
				
				
			 // Check user name duplication?
				$sql = "SELECT userName,userPassword, userPicture FROM user WHERE userId=$userId ";
				//$result_user = mysqli_query($link, $sql_user);
				//$is_user = mysqli_num_rows($result_user);
				
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();	
				//$result = $stmt->rowCount();
				

				if ($stmt->rowCount() <> 1){
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new username. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));  
				  exit;    
				}   
				$row=$stmt->fetch();
				
				$hash_userPassword='';
				if(isset($userPassword) AND $userPassword<>''){
					 // Encript New Password
					$salt = "asdadasgfd";
					$hash_userPassword = hash_hmac('sha256', $userPassword, $salt);
				}else{
					//Old Password
					$hash_userPassword=$row['userPassword'];
				}
				
			  
				//inputFile
				if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
					// If the old picture already exists, delete it
					if (file_exists('dist/img/'.$curPhoto)) unlink('dist/img/'.$curPhoto);
				
					$new_picture_name = 'user_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "dist/img/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}
				
				
				$sql = "UPDATE `user` SET `userName`=:userName 
				, `userPassword`=:userPassword
				, `userFullname`=:userFullname
				, `userEmail`=:userEmail 
				, `userTel`=:userTel
				, `userPicture`=:new_picture_name
				, `userGroupCode`=:userGroupCode
				, `smId`=:smId
				, `statusCode`=:statusCode 
				WHERE userId=:userId 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':userName', $userName);
				$stmt->bindParam(':userPassword', $hash_userPassword);
				$stmt->bindParam(':userFullname', $userFullname);
				$stmt->bindParam(':userEmail', $userEmail);
				$stmt->bindParam(':userTel', $userTel);
				$stmt->bindParam(':new_picture_name', $new_picture_name);
				$stmt->bindParam(':userGroupCode', $userGroupCode);
				$stmt->bindParam(':smId', $smId);
				$stmt->bindParam(':statusCode', $statusCode);
				$stmt->bindParam(':userId', $userId);
				;	
			 
				if ($stmt->execute()) {
				  header('Content-Type: application/json');
				  echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
			   } else {
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new username. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));
			}
				break;
			case 'setActive' :
				$id = $_POST['id'];
				$statusCode = $_POST['statusCode'];	
				
				$sql = "UPDATE `".$tb."` SET statusCode=:statusCode WHERE userId=:id ";
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
				
				$sql = "UPDATE `".$tb."` SET statusCode='X' WHERE userId=:id ";
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
					$id = $_GET['id'];
					
					$pdo->beginTransaction();
					
					//delete image
					$sql = "SELECT userPicture FROM user WHERE userId=:id ";
					$result_img = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();
					$row = $stmt->fetch();
					
					if (file_exists('dist/img/'.$row['userPicture'])) unlink('dist/img/'.$row['userPicture']); 

					$sql = "DELETE FROM user WHERE userId=:id ";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();

					$pdo->commit();	
					
					header("Location: user.php");
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