<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$search_fullname = $_POST['search_fullname'];
	$sql = "SELECT hdr.`docNo`, hdr.`refNo`, hdr.`rcNo`, hdr.`docDate`
	FROM `inv_ret` hdr
	WHERE 1=1 
	AND hdr.statusCode='P' 
	AND (hdr.rcNo IS NULL OR hdr.rcNo='') 
	AND hdr.docNo like :search_word ";
	$sql .= "ORDER BY hdr.createTime DESC
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


