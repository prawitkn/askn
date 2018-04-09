<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$search_fullname = $_POST['search_fullname'];
	$sql = "SELECT hdr.`soNo`, hdr.`saleDate`, hdr.`custId`, hdr.`smId`, hdr.`createTime`, hdr.`createById`, hdr.statusCode 
	, ct.name as custName, ct.addr1, ct.tel, ct.fax
	, c.name as smName 
	, d.userFullname as createByName 
	FROM `sale_header` hdr 
	left join customer ct on hdr.custId=ct.id 
	left join salesman c on hdr.smId=c.id 
	left join user d on hdr.createById=d.userId
	WHERE 1 
	AND hdr.statusCode='P' 
	AND hdr.isClose='N' 
	AND hdr.soNo like :search_word ";
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


