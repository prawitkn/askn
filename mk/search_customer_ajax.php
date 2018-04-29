<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$search_word = $_POST['search_word'];
	$sql = "SELECT h.`id`, h.`code`, h.`name`, h.`addr1`, h.`addr2`, h.`addr3`, h.`zipcode`,  h.`smId`, h.`smAdmId`, h.`creditDay` 	
	, sm.name as smName, sm2.name as smAdmName
	FROM `customer` h 
	LEFT JOIN salesman sm ON sm.id=h.smId
	LEFT JOIN salesman sm2 ON sm2.id=h.smAdmId 
	WHERE 1 
	AND h.statusCode='A' 
	AND h.name like :search_word ";
	switch($s_userGroupCode){ 
		case 'it' : 
		case 'admin' : 
			break;
		case 'sales' :
			$sql .= "AND h.smId=$s_smId ";
			break;
		case 'salesAdm' :
			$sql .= "AND h.smAdmId=$s_smId ";
			break;
		default :
	}	
	$sql .= "ORDER BY h.name ASC 
	";
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$search_word = '%'.$search_word.'%';
	$stmt->bindParam(':search_word', $search_word);
	$stmt->execute();
	
	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode($jsonData);
	
?>


