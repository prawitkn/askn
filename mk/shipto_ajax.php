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
	
	$tb='shipto';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				//$id = $_POST['id'];
				$custId = $_POST['custId'];
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
				$contact = $_POST['contact'];
				$contactPosition = $_POST['contactPosition'];
				$email = $_POST['email'];
				$tel = $_POST['tel']; 
				$fax = $_POST['fax']; 
				$smId = $_POST['smId']; 
				$smAdmId = (isset($_POST['smAdmId'])? $_POST['smAdmId'] : 0 );//if because column datatype = int
				$statusCode = (isset($_POST['statusCode'])? $_POST['statusCode'] : 'I' );
								
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
				$sql = "INSERT INTO `".$tb."`(`custId`, `code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`, `locationCode`, `creditDay`, `marketCode`
				, `contact`, `contactPosition`, `email`, `tel`, `fax`, `smId`, `smAdmId`
				, `statusCode`, `createTime`, `createById`) 
				 VALUES 
				(:custId,:code,:name,:addr1,:addr2,:addr3,:zipcode,:countryName,:locationCode,:creditDay,:marketCode
				,:contact,:contactPosition,:email,:tel,:fax,:smId,:smAdmId
				,'A', now(), :s_userId)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':custId', $custId); $stmt->bindParam(':code', $code); $stmt->bindParam(':name', $name); 
				$stmt->bindParam(':addr1', $addr1); $stmt->bindParam(':addr2', $addr2); $stmt->bindParam(':addr3', $addr3); 
				$stmt->bindParam(':zipcode', $zipcode); $stmt->bindParam(':countryName', $countryName); $stmt->bindParam(':locationCode', $locationCode); $stmt->bindParam(':creditDay', $creditDay);
				$stmt->bindParam(':marketCode', $marketCode); $stmt->bindParam(':contact', $contact); $stmt->bindParam(':contactPosition', $contactPosition); 
				$stmt->bindParam(':email', $email); $stmt->bindParam(':tel', $tel); $stmt->bindParam(':fax', $fax); 
				$stmt->bindParam(':smId', $smId); $stmt->bindParam(':smAdmId', $smAdmId); 
				$stmt->bindParam(':s_userId', $s_userId);
				//$stmt->execute();

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
				$custId = $_POST['custId'];
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
				$contact = $_POST['contact'];
				$contactPosition = $_POST['contactPosition'];
				$email = $_POST['email'];
				$tel = $_POST['tel']; 
				$fax = $_POST['fax']; 
				$smId = $_POST['smId']; 
				$smAdmId = (isset($_POST['smAdmId'])? $_POST['smAdmId'] : 0 );//if because column datatype = int
				$statusCode = (isset($_POST['statusCode'])? $_POST['statusCode'] : 'I' );
								
				$sql = "UPDATE `".$tb."` SET `custId`=:custId, `code`=:code, `name`=:name, `addr1`=:addr1, `addr2`=:addr2
				, `addr3`=:addr3, `zipcode`=:zipcode, `countryName`=:countryName, `locationCode`=:locationCode, `creditDay`=:creditDay, `marketCode`=:marketCode
				, `contact`=:contact, `contactPosition`=:contactPosition, `email`=:email, `tel`=:tel, `fax`=:fax, `smId`=:smId, `smAdmId`=:smAdmId
				, `statusCode`=:statusCode 				
				WHERE id=:id 
				";	
				$stmt = $pdo->prepare($sql);	
				$stmt->bindParam(':custId', $custId);
				$stmt->bindParam(':code', $code);
				$stmt->bindParam(':name', $name);
				$stmt->bindParam(':addr1', $addr1);
				$stmt->bindParam(':addr2', $addr2);
				$stmt->bindParam(':addr3', $addr3);
				$stmt->bindParam(':zipcode', $zipcode);
				$stmt->bindParam(':countryName', $countryName);
				$stmt->bindParam(':locationCode', $locationCode);
				$stmt->bindParam(':creditDay', $creditDay);
				$stmt->bindParam(':marketCode', $marketCode);
				$stmt->bindParam(':contact', $contact);
				$stmt->bindParam(':contactPosition', $contactPosition);
				
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':tel', $tel);
				$stmt->bindParam(':fax', $fax);
				
				
				$stmt->bindParam(':smId', $smId);
				$stmt->bindParam(':smAdmId', $smAdmId);
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