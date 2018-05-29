<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];
		$s_userID=$_SESSION['userID'];*/
	switch($s_userGroupCode){ 
		case 'whOff' :
		case 'whSup' :
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
			exit();
			break;
		default :	// it, admin 
	}	

	$search_word = $_POST['search_word'];

try{	
	$sql = "SELECT hdr.`rtNo`, hdr.`returnDate`, hdr.`fromCode`, hdr.`toCode`, hdr.`remark`, hdr.`statusCode`	
	, fsl.name as fromName, tsl.name as toName 
	FROM `rt` hdr
	LEFT JOIN sloc fsl on hdr.fromCode=fsl.code
	LEFT JOIN sloc tsl on hdr.toCode=tsl.code
	WHERE 1 
	AND hdr.statusCode='P' 
	AND hdr.rcNo='' 
	AND hdr.rtNo like :search_word ";
	switch($s_userGroupCode){ 
		case 'whOff' :
		case 'whSup' :
			$sql .= "AND hdr.toCode IN ('8','E') ";
			break;
		case 'pdOff' :
		case 'pdSup' :
			$sql .= "AND hdr.toCode=:s_userDeptCode ";
			break;
		default :	// it, admin 
	}	
	$sql .= "ORDER BY hdr.createTime DESC";
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



