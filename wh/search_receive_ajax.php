<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/

	$search_word = $_POST['search_word'];

try{	
	$sql = "SELECT hdr.`rcNo`, hdr.`refNo`, hdr.`receiveDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`statusCode`
	, hdr.`createTime`, hdr.`createByID`, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
	, fsl.name as fromName, tsl.name as toName 
	, cu.userFullname as createByName, fu.userFullname as confirmByName, pu.userFullname as approveByName 
	FROM `receive` hdr
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	LEFT JOIN user cu on hdr.createByID=cu.userId 
	LEFT JOIN user fu on hdr.confirmById=fu.userId
	LEFT JOIN user pu on hdr.approveById=pu.userId  
	WHERE 1 
	AND hdr.statusCode='P' 
	AND hdr.rcNo like :search_word ";
	switch($s_userGroupCode){ 
		case 'whOff' :
		case 'whSup' :
			$sql .= "AND hdr.toCode IN ('0','7','8','E') ";
			break;
		case 'pdOff' :
		case 'pdSup' :
			$sql .= "AND hdr.toCode=:s_userDeptCode ";
			break;
		default :	// it, admin 
	}	
	$sql .= "ORDER BY hdr.createTime DESC
	";
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$search_word = '%'.$search_word.'%';
	$stmt->bindParam(':search_word', $search_word);
	switch($s_userGroupCode){ 
		case 'pdOff' :
		case 'pdSup' :
			$stmt->bindParam(':s_userDeptCode', $s_userDeptCode);
			break;
		default :	// it, admin 
	}	
	$stmt->execute();

	$rowCount=$stmt->rowCount();
	
	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode(array('rowCount' => $rowCount, 'data' => json_encode($jsonData)));
} 
//Our catch block will handle any exceptions that are thrown.
catch(Exception $e){
	//Rollback the transaction.
    $pdo->rollBack();
	//return JSON
	header('Content-Type: application/json');
	$errors = "Error on data approval. Please try again. " . $e->getMessage();
	echo json_encode(array('success' => false, 'message' => $errors));
}	
?>


