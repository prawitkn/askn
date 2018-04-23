<?php
	include 'session.php'; /*$s_userID = $row_user['userID'];
        $s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_smCode = $row_user['smCode'];*/

	$search_fullname = $_POST['search_fullname'];
	
	$sql = "SELECT a.`doNo`, a.`soNo`, a.`ppNo`, a.`deliveryDate`, a.`remark`, a.`statusCode`, a.`createTime`, a.`createByID`
	, ct.custName, ct.custAddr, ct.custTel, ct.custFax
	, c.name as smName
	, d.userFullname as createByName
	FROM `delivery_header` a
	left join customer ct on a.custCode=ct.code ";
	switch($s_userGroupCode){
		case 'it' : case 'admin' : 
			break;
		case 'sales' : $sql .= " AND ct.smCode=:s_smCode "; break;
		case 'salesAdmin' : 	$sql .= " AND ct.smAdmCode=:s_smCode "; break;
		default : 
			//return JSON
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
			exit();
	}			
	$sql .= "left join salesman c on a.smCode=c.code
	left join user d on a.createByID=d.userID
	WHERE 1 
	AND a.statusCode='P'
	AND a.refInvNo='' 	
	ORDER BY a.createTime DESC
	";
	
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$search_fullname = '%'.$search_fullname.'%';
	$stmt->bindParam(':search_word', $search_fullname);
	switch($s_userGroupCode){
		case 'it' : case 'admin' : 
			break;
		case 'sales' : $stmt->bindParam(':s_smCode', $s_smCode);
			break;
		case 'salesAdmin' : $stmt->bindParam(':s_smCode', $s_smCode);
			break;
		default : 
	}	
	
	$stmt->execute();
	
	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode($jsonData);
	
?>


