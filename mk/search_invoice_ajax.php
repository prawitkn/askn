<?php
	include 'session.php'; /*$s_userID = $row_user['userID'];
        $s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_smCode = $row_user['smCode'];*/

	$search_word = $_POST['search_fullname'];
	
	$sql = "SELECT hdr.`invNo`, hdr.`doNo`, hdr.`refNo`, hdr.`invoiceDate`, hdr.`custCode`, hdr.`smCode`, hdr.`totalExcVat`
	, hdr.`vatAmount`, hdr.`totalIncVat`, hdr.`remark`, hdr.`statusCode`, hdr.`createTime`, hdr.`createById`
	, hdr.`updateTime`, hdr.`updateById`, hdr.`confirmTime`, hdr.`confirmById`, hdr.`approveTime`, hdr.`approveById`
	, ct.custName, ct.custAddr, ct.taxId, ct.creditDay 
	, sm.name as smName, concat(sm.name, '  ', sm.surname) as smFullname 
	, dh.remark as delivery_remark 
	, dh.soNo, sh.poNo 
	, uca.userFullname as createByName, ucf.userFullname as confirmByName, uap.userFullname as approveByName
	FROM invoice_header hdr 	
	INNER JOIN  delivery_header dh on dh.doNo=hdr.doNo 			
	INNER JOIN  prepare pa on pa.ppNo=dh.ppNo 				
	INNER JOIN  picking pi on pi.pickNo=pa.pickNo
	INNER JOIN sale_header sh on sh.soNo=pi.soNo 
	LEFT JOIN customer ct on ct.code=hdr.custCode ";

	$sql .= "
	LEFT JOIN salesman sm on sm.code=hdr.smCode 
	LEFT JOIN user uca on hdr.createByID=uca.userID
	LEFT JOIN user ucf on hdr.confirmByID=ucf.userID
	LEFT JOIN user uap on hdr.approveByID=uap.userID

	WHERE 1=1 
	AND hdr.statusCode='P'	
	";
	
	if(isset($_GET['search_word']) and isset($_GET['search_word'])){
		$search_word=$_GET['search_word'];
		$sql .= "AND (hdr.docNo like '%".$_GET['search_word']."%') ";
	}	
	$sql .= "ORDER BY hdr.createTime DESC ";
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	if(isset($_GET['search_word']) and isset($_GET['search_word'])){
		$stmt->bindParam(':search_word', $search_word);
	}	
	switch($s_userGroupCode){
		case 'it' : case 'admin' : 
			break;
		case 'sales' : $stmt->bindParam(':s_smCode', $s_userID);
			break;
		case 'salesAdmin' : $stmt->bindParam(':s_smCode', $s_userID);
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


