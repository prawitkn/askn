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
	
	$tb='customer';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				//$id = $_POST['id'];
				$code = $_POST['code'];
				$name = $_POST['name'];
				$addr1 = $_POST['addr1'];
				$addr2 = $_POST['addr2'];
				$addr3 = $_POST['addr3'];
				$zipcode = $_POST['zipcode'];
				$countryName = $_POST['countryName'];
				$soRemark = $_POST['soRemark'];
				$locationCode = $_POST['locationCode'];
				$creditDay = $_POST['creditDay'];
				$marketCode = $_POST['marketCode'];
				$contact = $_POST['contact'];
				$contactPosition = $_POST['contactPosition'];
				$email = $_POST['email'];
				$tel = $_POST['tel']; 
				$fax = $_POST['fax']; 
				$smId = $_POST['smId']; 
				$smAdmId = (isset($_POST['smAdmId'])? $_POST['smAdmId'] : 0 );//if because column datatype = int
				$statusCode = (isset($_POST['statusCode'])? $_POST['statusCode'] : '' );

			try{	
				//We start our transaction.
				$pdo->beginTransaction();
				 //Check Duplicate customer
				 $sql = "SELECT * FROM `customer` WHERE code=:code OR `name`=:name LIMIT 1 "; 
				 $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':code', $code); $stmt->bindParam(':name', $name); 
				$stmt->execute();
				if($stmt->rowCount()>=1){
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Duplicate Customer data.'));
					exit;
				}	
				//Check Duplicate shipto
				 $sql = "SELECT * FROM `shipto` WHERE code=:code OR `name`=:name LIMIT 1 "; 
				 $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':code', $code); $stmt->bindParam(':name', $name); 
				$stmt->execute();
				if($stmt->rowCount()>=1){
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Duplicate Shipto data.'));
					exit;
				}		
				//INsert customer
				$sql = "INSERT INTO `customer`(`code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`, `locationCode`, `creditDay`, `marketCode`, `soRemark`
				, `contact`, `contactPosition`, `email`, `tel`, `fax`, `smId`, `smAdmId`
				, `statusCode`, `createTime`, `createById`) 
				 VALUES 
				(:code,:name,:addr1,:addr2,:addr3,:zipcode,:countryName,:locationCode,:creditDay,:marketCode,:soRemark
				,:contact,:contactPosition,:email,:tel,:fax,:smId,:smAdmId
				,'A', now(), :s_userId)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':code', $code); 
				$stmt->bindParam(':name', $name); 
				$stmt->bindParam(':addr1', $addr1); 
				$stmt->bindParam(':addr2', $addr2); 
				$stmt->bindParam(':addr3', $addr3); 
				$stmt->bindParam(':zipcode', $zipcode); 
				$stmt->bindParam(':countryName', $countryName); 
				$stmt->bindParam(':locationCode', $locationCode); 
				$stmt->bindParam(':marketCode', $marketCode); 
				$stmt->bindParam(':soRemark', $soRemark); 
				$stmt->bindParam(':creditDay', $creditDay); 
				$stmt->bindParam(':contact', $contact); 
				$stmt->bindParam(':contactPosition', $contactPosition); 
				$stmt->bindParam(':email', $email); 
				$stmt->bindParam(':tel', $tel); 
				$stmt->bindParam(':fax', $fax); 
				$stmt->bindParam(':smId', $smId); 
				$stmt->bindParam(':smAdmId', $smAdmId); 
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->execute();
				
				$custId=$pdo->lastInsertId();
				//insert shipto.
				$sql = "INSERT INTO `shipto`(`custId`, `code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`, `locationCode`, `marketCode`
				, `contact`, `contactPosition`, `email`, `tel`, `fax`
				, `statusCode`, `createTime`, `createById`)  
				SELECT :custId, `code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`, `locationCode`, `marketCode`
				, `contact`, `contactPosition`, `email`, `tel`, `fax`
				, `statusCode`, `createTime`, `createById`
				FROM customer
				WHERE id=:custId2 ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':custId', $custId); 
				$stmt->bindParam(':custId2', $custId); 
				$stmt->execute();
				
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				 //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
			}catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Insertion. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}		
				break;
				exit();
			case 'edit' :
				try{
					//We start our transaction.
					$pdo->beginTransaction();
					
					$id = $_POST['id'];
					$code = $_POST['code'];
					$name = $_POST['name'];
					$addr1 = $_POST['addr1'];
					$addr2 = $_POST['addr2'];
					$addr3 = $_POST['addr3'];
					$zipcode = $_POST['zipcode'];
					$countryName = $_POST['countryName'];
					$locationCode = $_POST['locationCode'];
					$creditDay = $_POST['creditDay'];
					$marketCode = $_POST['marketCode'];
					$soRemark = $_POST['soRemark'];
					$contact = $_POST['contact'];
					$contactPosition = $_POST['contactPosition'];
					$email = $_POST['email'];
					$tel = $_POST['tel']; 
					$fax = $_POST['fax']; 
					$smId = (isset($_POST['smId'])? $_POST['smId'] : 0 ); //if because column datatype = int
					$smAdmId = (isset($_POST['smAdmId'])? $_POST['smAdmId'] : 0 );
					$statusCode = (isset($_POST['statusCode'])? $_POST['statusCode'] : 'I' );

					//insert backup
					$sql="INSERT INTO `customer_history` 
					SELECT *, now(), :s_userId FROM customer WHERE id=:id ";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':s_userId', $s_userId); $stmt->bindParam(':id', $id); 
					$stmt->execute();
					
					//update 
					$sql = "UPDATE `customer` SET  `code`=:code, `name`=:name 
					, `addr1`=:addr1, `addr2`=:addr2, `addr3`=:addr3
					, `zipcode`=:zipcode, `countryName`=:countryName
					, `creditDay`=:creditDay
					, `locationCode`=:locationCode, `marketCode`=:marketCode, `soRemark`=:soRemark
					, `contact`=:contact, `contactPosition`=:contactPosition
					, `email`=:email, `tel`=:tel, `fax`=:fax	
					, `smId`=:smId, `smAdmId`=:smAdmId 	
					, `statusCode`=:statusCode	
					WHERE id=:id 
					";
				 
					//$result = mysqli_query($link, $sql);
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':code', $code); 
					$stmt->bindParam(':name', $name); 
					$stmt->bindParam(':addr1', $addr1); 
					$stmt->bindParam(':addr2', $addr2); 
					$stmt->bindParam(':addr3', $addr3); 
					$stmt->bindParam(':zipcode', $zipcode); 
					$stmt->bindParam(':countryName', $countryName); 
					$stmt->bindParam(':locationCode', $locationCode); 
					$stmt->bindParam(':marketCode', $marketCode); 
					$stmt->bindParam(':creditDay', $creditDay); 					
					$stmt->bindParam(':soRemark', $soRemark); 
					$stmt->bindParam(':contact', $contact); 
					$stmt->bindParam(':contactPosition', $contactPosition); 
					$stmt->bindParam(':email', $email); 
					$stmt->bindParam(':tel', $tel); 
					$stmt->bindParam(':fax', $fax); 
					$stmt->bindParam(':smId', $smId); 
					$stmt->bindParam(':smAdmId', $smAdmId); 
					$stmt->bindParam(':statusCode', $statusCode);
					$stmt->bindParam(':id', $id);
					$stmt->execute();
					
					//Commit
					$pdo->commit();
					
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data Update Complete.'));

				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//Rollback the transaction.
					$pdo->rollBack();
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data updation. Please try again. " . $e->getMessage();
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

			case 'getShipToList' :
				$id = $_POST['id'];
				
				$sql = "SELECT `custId`, `id`, `code`, `name`, `addr1`, `addr2`, `addr3`, `contact`, `contactPosition`, `zipcode`, `email`, `tel`, `fax`, `statusCode`, `createTime`, `createById`
				FROM `shipto` WHERE custId=:id ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				
				$rowCount=$stmt->rowCount();
				$jsonData = array();
				while($row=$stmt->fetch()){						
						$jsonData[] = $row;
				}
				
				header('Content-Type: application/json');				
				echo json_encode( array('success' => true, 'rowCount' => $rowCount, 'data' => json_encode($jsonData) ) );				
				break;

			case 'getShipTo' :
				$id = $_POST['id'];
				
				$sql = "SELECT `custId`, `id`, `code`, `name`, `addr1`, `addr2`, `addr3`, `contact`, `contactPosition`, `zipcode`, `countryName`, `email`, `tel`, `fax`, `statusCode`, `createTime`, `createById`
				FROM `shipto` WHERE id=:id ";
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				
				$rowCount=$stmt->rowCount();
				$jsonData = $stmt->fetch();
				
				header('Content-Type: application/json');				
				echo json_encode( array('success' => true, 'rowCount' => $rowCount, 'data' => json_encode($jsonData) ) );				
				break;
					
			case 'shipToSave' :
				try{
					$custId = $_POST['custId'];
					$id = $_POST['itmId'];
					$code = $_POST['itmCode'];
					$name = $_POST['itmName'];
					$addr1 = $_POST['itmAddr1'];
					$addr2 = $_POST['itmAddr2'];
					$addr3 = $_POST['itmAddr3'];
					$zipcode = $_POST['itmZipcode'];
					$countryName = $_POST['itmCountryName'];
					$contact = $_POST['itmContact'];
					$contactPosition = $_POST['itmContactPosition'];
					$email = $_POST['itmEmail'];
					$tel = $_POST['itmTel']; 
					$fax = $_POST['itmFax']; 
					$statusCode = (isset($_POST['itmStatusCode'])? $_POST['itmStatusCode'] : 'I' );			
										
					
					//We start our transaction.
					$pdo->beginTransaction();
					
					if($id==""){
						//Check Duplicate shipto
						 $sql = "SELECT * FROM `shipto` WHERE code=:code OR `name`=:name LIMIT 1 "; 
						 $stmt = $pdo->prepare($sql);
						$stmt->bindParam(':code', $code); $stmt->bindParam(':name', $name); 
						$stmt->execute();
						if($stmt->rowCount()>=1){
							header('Content-Type: application/json');
							echo json_encode(array('success' => false, 'message' => 'Duplicate Shipto data.'));
							exit;
						}		
						//INsert customer
						$sql = "INSERT INTO `shipto`(`custId`, `code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`
						, `contact`, `contactPosition`, `email`, `tel`, `fax`
						, `statusCode`, `createTime`, `createById`) 
						 VALUES 
						(:custId, :code,:name,:addr1,:addr2,:addr3,:zipcode,:countryName
						,:contact,:contactPosition,:email,:tel,:fax
						,'A', now(), :createById)";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':custId', $custId); 
						$stmt->bindParam(':code', $code); 
						$stmt->bindParam(':name', $name); 
						$stmt->bindParam(':addr1', $addr1); 
						$stmt->bindParam(':addr2', $addr2); 
						$stmt->bindParam(':addr3', $addr3); 
						$stmt->bindParam(':zipcode', $zipcode);
						$stmt->bindParam(':countryName', $countryName); 
						$stmt->bindParam(':contact', $contact); 
						$stmt->bindParam(':contactPosition', $contactPosition); 
						$stmt->bindParam(':email', $email); 
						$stmt->bindParam(':tel', $tel); 
						$stmt->bindParam(':fax', $fax); 
						$stmt->bindParam(':createById', $s_userId);
						$stmt->execute();
					}else{
						//Check Duplicate shipto
						 $sql = "SELECT * FROM `shipto` WHERE (code=:code OR `name`=:name) AND id<>:id LIMIT 1 "; 
						 $stmt = $pdo->prepare($sql);
						$stmt->bindParam(':code', $code); 
						$stmt->bindParam(':name', $name); 
						$stmt->bindParam(':id', $id); 
						$stmt->execute();
						$stmt->execute();
						if($stmt->rowCount()>=1){
							header('Content-Type: application/json');
							echo json_encode(array('success' => false, 'message' => 'Duplicate Shipto data.'));
							exit;
						}		

						//update 
						$sql = "UPDATE `shipto` SET  `code`=:code, `name`=:name 
						, `addr1`=:addr1, `addr2`=:addr2, `addr3`=:addr3
						, `zipcode`=:zipcode, `countryName`=:countryName
						, `contact`=:contact, `contactPosition`=:contactPosition
						, `email`=:email, `tel`=:tel, `fax`=:fax	
						, `statusCode`=:statusCode	
						WHERE id=:id 
						";
					 
						//$result = mysqli_query($link, $sql);
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':code', $code); 
						$stmt->bindParam(':name', $name); 
						$stmt->bindParam(':addr1', $addr1); 
						$stmt->bindParam(':addr2', $addr2); 
						$stmt->bindParam(':addr3', $addr3); 
						$stmt->bindParam(':zipcode', $zipcode); 
						$stmt->bindParam(':countryName', $countryName); 
						$stmt->bindParam(':contact', $contact); 
						$stmt->bindParam(':contactPosition', $contactPosition); 
						$stmt->bindParam(':email', $email); 
						$stmt->bindParam(':tel', $tel); 
						$stmt->bindParam(':fax', $fax); 
						$stmt->bindParam(':statusCode', $statusCode);
						$stmt->bindParam(':id', $id);
						$stmt->execute();	
							
					}					
		
					$pdo->commit();
					
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Success.', 'id' => $id));
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//Rollback the transaction.
					$pdo->rollBack();
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error : " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}	
				break;
			case 'itemSetActive' :
				$id = $_POST['id'];
				$statusCode = $_POST['statusCode'];	
				
				$sql = "UPDATE `shipto` SET statusCode=:statusCode WHERE id=:id ";
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

			default : 
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
		}
	}