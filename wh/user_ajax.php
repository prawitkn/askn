<?php
    include 'session.php';	
		
	$tb='wh_user';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				$userFullname = $_POST['userFullname'];
				$userName = $_POST['userName'];
				$userPassword = mysqli_real_escape_string($link, $_POST['userPassword']);
				$userPin = mysqli_real_escape_string($link, $_POST['userPin']);
				$userEmail = $_POST['userEmail'];
				$userTel = $_POST['userTel'];
				$userGroupCode = $_POST['userGroupCode'];
				$userDeptCode = $_POST['userDeptCode'];
				
			 // Check user name duplication?
				$sql_user = "SELECT userName FROM ".$tb." WHERE userName='$userName'";
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

				// Encript PIN
				$salt = "asdadasgfd";
				$hash_userPin = hash_hmac('sha256', $userPin, $salt);

				$new_picture_name="";
				 // Upload Picture
				if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
					$new_picture_name = 'user_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "dist/img/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}
				
				
				$sql = "INSERT INTO ".$tb." (`userName`, `userPassword`, `userPin`, `userFullname`, `userEmail`, `userTel`, `userPicture`, `userGroupCode`,  `userDeptCode`, `statusCode`)"
						. " VALUES ('$userName', '$hash_userPassword', '$hash_userPin', '$userFullname', '$userEmail', '$userTel', '$new_picture_name', '$userGroupCode', '$userDeptCode','A')";
						 
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
				$userPin = $_POST['userPin'];
				$userEmail = $_POST['userEmail'];
				$userTel = $_POST['userTel'];
				$userGroupCode = $_POST['userGroupCode'];
				$userDeptCode = $_POST['userDeptCode'];
				$statusCode = $_POST['statusCode'];
				$loginStatus = $_POST['loginStatus'];
				
				$curPhoto = $_POST['curPhoto'];
				$new_picture_name=$curPhoto;
				
				
				
			 // Check user name duplication?
				$sql = "SELECT userName,userPassword,userPin, userPicture FROM ".$tb." WHERE userId=$userId ";
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

				$hash_userPin='';
				if(isset($userPin) AND $userPin<>''){
					 // Encript New Password
					$salt = "asdadasgfd";
					$hash_userPin = hash_hmac('sha256', $userPin, $salt);
				}else{
					//Old Password
					$hash_userPin=$row['userPin'];
				}
				
			  
				//inputFile
				if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
					// If the old picture already exists, delete it
					if (file_exists('dist/img/'.$curPhoto)) unlink('dist/img/'.$curPhoto);
				
					$new_picture_name = 'user_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "dist/img/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}
				
				
				$sql = "UPDATE `wh_user` SET `userName`=:userName 
				, `userPassword`=:userPassword
				, `userPin`=:userPin
				, `userFullname`=:userFullname
				, `userEmail`=:userEmail 
				, `userTel`=:userTel
				, `userPicture`=:new_picture_name
				, `userGroupCode`=:userGroupCode
				, `userDeptCode`=:userDeptCode
				, `statusCode`=:statusCode 
				, `loginStatus`=:loginStatus 
				WHERE userId=:userId 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':userName', $userName);
				$stmt->bindParam(':userPassword', $hash_userPassword);
				$stmt->bindParam(':userPin', $hash_userPin);
				$stmt->bindParam(':userFullname', $userFullname);
				$stmt->bindParam(':userEmail', $userEmail);
				$stmt->bindParam(':userTel', $userTel);
				$stmt->bindParam(':new_picture_name', $new_picture_name);
				$stmt->bindParam(':userGroupCode', $userGroupCode);
				$stmt->bindParam(':userDeptCode', $userDeptCode);
				$stmt->bindParam(':statusCode', $statusCode);
				$stmt->bindParam(':loginStatus', $loginStatus);
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
					$id = $_POST['id'];
					
					$pdo->beginTransaction();
					
					//delete image
					$sql = "SELECT userPicture FROM ".$tb." WHERE userId=:id ";
					$result_img = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();
					$row = $stmt->fetch();
					
					if (file_exists('dist/img/'.$row['userPicture'])) unlink('dist/img/'.$row['userPicture']); 

					$sql = "DELETE FROM ".$tb." WHERE userId=:id ";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();

					$pdo->commit();	
					
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data Delete Completed.'));
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