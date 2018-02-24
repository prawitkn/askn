<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$id = $_POST['id'];
	$sql = "SELECT hdr.* FROM shipto hdr WHERE statusCode='A' AND custId=:id ";
	switch($s_userGroupCode){ 
		case 'it' : 
		case 'admin' : 
			break;
		case 'sales' :
			$sql .= "AND hdr.smId=$s_smId ";
			break;
		case 'salesAdm' :
			$sql .= "AND hdr.smAdmId=$s_smId ";
			break;
		default :
	}	
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


