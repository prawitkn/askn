<?php
	include 'inc_helper.php'; 
    //include 'db.php';
	include 'session.php';							

	//$id = $_POST['id'];
	$code = $_POST['code'];
	$name = $_POST['name'];
	$addr1 = $_POST['addr1'];
	$addr2 = $_POST['addr2'];
	$addr3 = $_POST['addr3'];
	$zipcode = $_POST['zipcode'];
	$countryName = $_POST['countryName'];
	$locationCode = $_POST['locationCode'];
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
    $sql = "INSERT INTO `customer`(`code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`, `locationCode`, `marketCode`
	, `contact`, `contactPosition`, `email`, `tel`, `fax`, `smId`, `smAdmId`
	, `statusCode`, `createTime`, `createById`) 
	 VALUES 
	(:code,:name,:addr1,:addr2,:addr3,:zipcode,:countryName,:locationCode,:marketCode
	,:contact,:contactPosition,:email,:tel,:fax,:smId,:smAdmId
	,:statusCode, now(), :s_userId)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code); $stmt->bindParam(':name', $name); 
	$stmt->bindParam(':addr1', $addr1); $stmt->bindParam(':addr2', $addr2); $stmt->bindParam(':addr3', $addr3); 
	$stmt->bindParam(':zipcode', $zipcode); $stmt->bindParam(':countryName', $countryName); $stmt->bindParam(':locationCode', $locationCode); $stmt->bindParam(':marketCode', $marketCode); 
	$stmt->bindParam(':contact', $contact); $stmt->bindParam(':contactPosition', $contactPosition); 
	$stmt->bindParam(':email', $email); $stmt->bindParam(':tel', $tel); $stmt->bindParam(':fax', $fax); 
	$stmt->bindParam(':smId', $smId); $stmt->bindParam(':smAdmId', $smAdmId); 
	$stmt->bindParam(':statusCode', $statusCode);
	$stmt->bindParam(':s_userId', $s_userId);
	$stmt->execute();
	
	$custId=$pdo->lastInsertId();
	//insert shipto.
	$sql = "INSERT INTO `shipto`(`custId`, `code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`, `locationCode`, `marketCode`
	, `contact`, `contactPosition`, `email`, `tel`, `fax`, `smId`, `smAdmId`
	, `statusCode`, `createTime`, `createById`)  
	SELECT :custId, `code`, `name`, `addr1`, `addr2`, `addr3`, `zipcode`, `countryName`, `locationCode`, `marketCode`
	, `contact`, `contactPosition`, `email`, `tel`, `fax`, `smId`, `smAdmId`
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
?>