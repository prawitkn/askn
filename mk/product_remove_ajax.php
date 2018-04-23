<?php

include 'session.php';

$id = $_POST['id'];

$sql = "UPDATE m_product SET statusCode='X' 
		WHERE id=$id";

$result = mysqli_query($link, $sql);

if ($result) {
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data deleted'));
} else {
	header('Content-Type: application/json');
	$errors = "Error on Data Remove. Please try again. " . mysqli_error($link);
	echo json_encode(array('success' => false, 'message' => $errors));
}		

?>     

