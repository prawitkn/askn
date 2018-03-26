<?php
	include 'session.php';

	$search_word = $_POST['search_word'];
	
	$sql = "SELECT a.`doNo`, a.`soNo`, a.`ppNo`, a.`deliveryDate`, a.`remark`, a.`statusCode`, a.`createTime`, a.`createById`
	, ct.name as custName 
	, c.name as smName
	, d.userFullname as createByName
	FROM `delivery_header` a
	INNER join customer ct on a.custId=ct.id  ";
	$sql.="AND ct.name like :search_word ";
	switch($s_userGroupCode){
		case 'it' : case 'admin' : 
			break;
		case 'sales' : $sql .= " AND ct.smId=:s_smId "; break;
		case 'salesAdmin' : 	//$sql .= " AND ct.smAdmId=:s_smId "; break;
		default : 
			//return JSON
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
			exit();
	}			
	$sql .= "left join salesman c on a.smId=c.id
	left join user d on a.createById=d.userId
	WHERE 1 
	AND a.statusCode='P' ";
	//AND a.refInvNo<>'' 	
	//ORDER BY a.createTime DESC
	//";
	
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$search_word = '%'.$search_word.'%';
	$stmt->bindParam(':search_word', $search_word);
	switch($s_userGroupCode){
		case 'it' : case 'admin' : 
			break;
		case 'sales' : $stmt->bindParam(':s_smId', $s_smId);
			break;
		case 'salesAdmin' : //$stmt->bindParam(':s_smId', $s_smId);
			break;
		default : 
	}	
	
	$stmt->execute();
	
	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode($jsonData);
	



