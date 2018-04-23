<?php

include 'session.php'; /*$s_userID=$_SESSION['userID'];
		$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

try{
	$id = $_POST['id'];

	//SQL 
	$sql = "DELETE FROM inv_ret_detail
			WHERE id=:id";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();

	//Return JSON
	header('Content-Type: application/json');
	echo json_encode(array('success' => true, 'message' => 'Data deleted'));
}catch(Exception $e){
	//Return JSON
	header('Content-Type: application/json');
	$errors = "Error on Data Deletion. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}		

?>     

