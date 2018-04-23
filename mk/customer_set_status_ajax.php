<?php

include 'session.php';

$id = $_POST['id'];
$statusCode = $_POST['statusCode'];

$sql = "UPDATE customer SET statusCode=:statusCode
WHERE id=:id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':statusCode', $statusCode);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data updated'));
} else {
	header('Content-Type: application/json');
	$errors = "Error on Data updated. Please try again. " .$stmt->errorInfo();
	echo json_encode(array('success' => false, 'message' => $errors));
}		

?>     

