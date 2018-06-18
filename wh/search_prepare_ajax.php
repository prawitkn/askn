<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$search_fullname = $_POST['search_fullname'];
	$sql = "SELECT 
	pp.ppNo, pp.prepareDate, pp.statusCode 
	,pk.pickNo
	,hdr.`soNo`, hdr.remark as salesRemark 
	,ct.code as custCode, ct.name as custName
	,c.name as smName
	,d.userFullname as createByName
	FROM prepare pp 
	left join picking pk on pk.pickNo=pp.pickNo 
	left join `sale_header` hdr on hdr.soNo=pk.soNo 
	left join customer ct on hdr.custId=ct.id 
	left join salesman c on hdr.smId=c.id
	left join wh_user d on pp.createById=d.userId
	WHERE 1 
	AND pp.statusCode='P' 
	AND pp.ppNo NOT IN (SELECT ppNo FROM delivery_header WHERE statusCode<>'X' )
	AND (pp.ppNo like :search_word or pp.pickNo like :search_word2)  ";

	$sql .= "ORDER BY pp.createTime DESC
	";
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$search_fullname = '%'.$search_fullname.'%';
	$stmt->bindParam(':search_word', $search_fullname);
	$stmt->bindParam(':search_word2', $search_fullname);
	$stmt->execute();

	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode($jsonData);
	
?>


