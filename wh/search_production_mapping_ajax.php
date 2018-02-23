<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$search_fullname = $_POST['search_fullname'];
	$sql = "SELECT hdr.id as prodId,  hdr.code as prodCode, hdr.catCode as prodCatCode,  hdr.name as prodName 
	FROM `product` hdr
	WHERE 1=1 
	AND hdr.statusCode='A' 
	AND hdr.code like :search_word ";
	$sql .= "ORDER BY hdr.code, hdr.catCode 
	";
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$search_fullname = '%'.$search_fullname.'%';
	$stmt->bindParam(':search_word', $search_fullname);
	$stmt->execute();

	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode($jsonData);
	
?>


