<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$search_fullname = $_POST['search_fullname'];
	$sql = "SELECT hdr.pickNo, hdr.pickDate, hdr.soNo, cust.name as custName 								
	FROM `picking` hdr
	LEFT JOIN sale_header s on s.soNo=hdr.soNo 
	LEFT JOIN customer cust on cust.id=s.custId
	left join user d on hdr.createById=d.userId
	WHERE 1 
	AND hdr.statusCode='P' ";
	$sql .= "ORDER BY hdr.approveTime  DESC
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


