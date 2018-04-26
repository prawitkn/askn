<?php
	//include 'inc_helper.php'; 
    //include 'db.php';
	include 'session.php';

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
	$contact = $_POST['contact'];
	$contactPosition = $_POST['contactPosition'];
	$email = $_POST['email'];
	$tel = $_POST['tel']; 
	$fax = $_POST['fax']; 
	$smId = (isset($_POST['smId'])? $_POST['smId'] : 0 ); //if because column datatype = int
	$smAdmId = (isset($_POST['smAdmId'])? $_POST['smAdmId'] : 0 );
	$statusCode = (isset($_POST['statusCode'])? $_POST['statusCode'] : '' );

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
	, `locationCode`=:locationCode, `marketCode`=:marketCode
	, `contact`=:contact, `contactPosition`=:contactPosition
	, `email`=:email, `tel`=:tel, `fax`=:fax	
	, `smId`=:smId, `smAdmId`=:smAdmId 	
	, `statusCode`=:statusCode	
	WHERE id=:id 
	";
 
    //$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code); $stmt->bindParam(':name', $name); 
	$stmt->bindParam(':addr1', $addr1); $stmt->bindParam(':addr2', $addr2); $stmt->bindParam(':addr3', $addr3); 
	$stmt->bindParam(':zipcode', $zipcode); $stmt->bindParam(':countryName', $countryName); $stmt->bindParam(':locationCode', $locationCode); $stmt->bindParam(':marketCode', $marketCode); 
	$stmt->bindParam(':creditDay', $creditDay); $stmt->bindParam(':contact', $contact); $stmt->bindParam(':contactPosition', $contactPosition); 
	$stmt->bindParam(':email', $email); $stmt->bindParam(':tel', $tel); $stmt->bindParam(':fax', $fax); 
	$stmt->bindParam(':smId', $smId); $stmt->bindParam(':smAdmId', $smAdmId); 
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
?>