<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$id = $_POST['id'];
	$sql = "SELECT hdr.`id`, hdr.`name` FROM product_roll_length hdr WHERE hdr.statusCode='A' AND hdr.prodId=:id ";
	$sql .= "ORDER BY hdr.name ASC 
	";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	
	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode($jsonData);
	
?>


