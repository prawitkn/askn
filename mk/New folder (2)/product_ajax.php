<?php
    include 'session.php';	
		
	$tb='product';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				//$id = $_POST['id'];
				$code = trim($_POST['code']);
				$catCode = $_POST['catCode'];
				$name = trim($_POST['name']);
				$name2 = ""; //trim($_POST['name2']);
				$uomCode = trim($_POST['uomCode']);
				$sourceTypeCode = $_POST['sourceTypeCode']; 
				$appCode = $_POST['appCode'];
				$price = 0; // $_POST['price'];
				$appCode = $_POST['appCode'];
				$description = $_POST['description'];	
				$statusCode = (isset($_POST['statusCode'])? 'A' : 'I' );
				
				$curPhoto = $_POST['curPhoto'];
				$curPdf = $_POST['curPdf'];
					
				$new_picture_name=$curPhoto;
				$new_pdf_name=$curPdf;
				 
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
					//if (file_exists('../images/product/'.$curPhoto)) unlink('../images/product/'.$curPhoto);
				
					$new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
					$path_upload = "../images/product/".$new_picture_name;
					move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
				}

				if (is_uploaded_file($_FILES['inputFile2']['tmp_name'])){
					// If the old picture already exists, delete it
					//if (file_exists('../images/product/'.$curPhoto)) unlink('../images/product/'.$curPhoto);
				
					$new_pdf_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile2']['name']), PATHINFO_EXTENSION);
					$path_upload = "../pdf/product/".$new_pdf_name;
					move_uploaded_file($_FILES['inputFile2']['tmp_name'], $path_upload);        
				}
					
				$sql = "INSERT INTO `product`(`code`, `catCode`, `name`, `uomCode`
				, `sourceTypeCode`, `appCode`, `photo`, `specFile`, `description`, `statusCode`, `createTime`, `createById`)  
				VALUES (	
				:code,:catCode,:name,:uomCode,:sourceTypeCode,:appCode,:photo,:specFile,:description
				,:statusCode, now(), :s_userId  
				)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':code', $code); 
				$stmt->bindParam(':catCode', $catCode); 
				$stmt->bindParam(':name', $name); 
				//$stmt->bindParam(':name2', $name2); 
				$stmt->bindParam(':uomCode', $uomCode); 
				$stmt->bindParam(':sourceTypeCode', $sourceTypeCode); 
				$stmt->bindParam(':appCode', $appCode); 
				$stmt->bindParam(':photo', $new_picture_name); 
				$stmt->bindParam(':specFile', $new_pdf_name); 
				//$stmt->bindParam(':price', $price); 
				$stmt->bindParam(':description', $description); 
				$stmt->bindParam(':statusCode', $statusCode); 
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
				//`id`, `code`, `catCode`, `name`, `name2`, `uomCode`, `ratioPack`, `packUomCode`
				//, `sourceTypeCode`, `appCode`, `isFg`, `isWip`, `photo`, `price`, `description`, `statusCode`
					$id = $_POST['id'];
					$code = $_POST['code'];
					$catCode = $_POST['catCode'];
					$name = $_POST['name'];
					$name2 = ""; // $_POST['name2'];
					$uomCode = $_POST['uomCode'];
					$sourceTypeCode = $_POST['sourceTypeCode']; 
					$appCode = $_POST['appCode'];
					$price = 0; //$_POST['price'];
					$appCode = $_POST['appCode'];
					$description = $_POST['description'];	
					$statusCode = (isset($_POST['statusCode'])? 'A' : 'I' );
					
					$curPhoto = $_POST['curPhoto'];
					$curPdf = $_POST['curPdf'];
						
					$new_picture_name=$curPhoto;
					$new_pdf_name=$curPdf;
					 
					
					/*$fileName = $_FILES['inputFile']['name'];
					//$fileExt = pathinfo($_FILES["inputFile"]["name"], PATHINFO_EXTENSION);
					$filePath = "files/".$fileName;
					if (move_uploaded_file($_FILES["inputFile"]["tmp_name"], $filePath)) {
						echo "Upload success";
					} else {
						echo "Upload failed";
					}*/
					
					
					 // Upload Picture
					if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
						// If the old picture already exists, delete it
						if (file_exists('../images/product/'.$curPhoto)) unlink('../images/product/'.$curPhoto);
					
						$new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
						$path_upload = "../images/product/".$new_picture_name;
						move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
					}
					

					if (is_uploaded_file($_FILES['inputFile2']['tmp_name'])){
						// If the old picture already exists, delete it
						if (file_exists('../pdf/product/'.$curPdf)) unlink('../pdf/product/'.$curPdf);
					
						$new_pdf_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile2']['name']), PATHINFO_EXTENSION);
						$path_upload = "../pdf/product/".$new_pdf_name;
						move_uploaded_file($_FILES['inputFile2']['tmp_name'], $path_upload);        
					}


				//`id`, `code`, `catCode`, `name`, `name2`, `uomCode`, `ratioPack`, `packUomCode`
				//, `sourceTypeCode`, `appCode`, `isFg`, `isWip`, `photo`, `price`, `description`, `statusCode`

					$sql = "UPDATE `product` SET `code`=:code
					, `catCode`=:catCode
					, `name`=:name
					, `uomCode`=:uomCode
					, `sourceTypeCode`=:sourceTypeCode
					, `appCode`=:appCode
					, `photo`=:new_picture_name
					, `specFile`=:new_pdf_name
					, `description`=:description
					, `statusCode`=:statusCode
					WHERE id=:id
					"; 
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':code', $code);
					$stmt->bindParam(':catCode', $catCode);
					$stmt->bindParam(':name', $name);
					$stmt->bindParam(':uomCode', $uomCode);
					$stmt->bindParam(':sourceTypeCode', $sourceTypeCode);
					$stmt->bindParam(':appCode', $appCode);
					$stmt->bindParam(':new_picture_name', $new_picture_name);
					$stmt->bindParam(':new_pdf_name', $new_pdf_name);
					$stmt->bindParam(':description', $description);
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
					$sql = "SELECT photo FROM product WHERE id=:id ";
					$result_img = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();
					$row = $stmt->fetch();
					
					if($row['photo']<>""){
						if (file_exists('../images/product/'.$row['photo'])) unlink('../images/product/'.$row['photo']);
					}

					$sql = "DELETE FROM product WHERE id=:id ";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);
					$stmt->execute();

					$pdo->commit();	
					
					//header("Location: product.php");
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