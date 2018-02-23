<?php
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	$search_word = $_POST['search_word'];
	$sql = "SELECT hdr.id as prodId, hdr.code as `prodCode`, hdr.catCode as `prodCatCode`, hdr.name as `prodName`, hdr.name2 as `prodName2`, hdr.uomCode as `prodUomCode`, hdr.`photo`, hdr.price as `prodPrice`
	, hdr.description as `prodDesc`, hdr.appCode as `prodAppCode`, hdr.`statusCode` 
	, cat.name as prodCatName, mk.name as prodAppName  
	, IFNULL(sb.balance,0) as balance, IFNULL(sb.sales,0) as sales
	FROM `product` hdr
	LEFT JOIN product_category cat on cat.code=hdr.catCode
	LEFT JOIN market mk on mk.code=hdr.appCode
	LEFT JOIN stk_bal sb ON sb.prodId=hdr.id AND sb.sloc='8' 
	
	WHERE 1
	AND hdr.statusCode='A' 
	AND hdr.code like :search_word ";
	/*switch($s_userGroupCode){ 
		case 'it' : 
		case 'admin' : 
			break;
		case 'warehouse' :
		case 'production' :
			$sql .= "AND hdr.toCode=$s_userDept ";
			break;
		default :
	}*/	
	$sql .= "ORDER BY hdr.name ASC 
	";
	//$result = mysqli_query($link, $sql);
	$stmt = $pdo->prepare($sql);
	$search_word = '%'.$search_word.'%';
	$stmt->bindParam(':search_word', $search_word);
	$stmt->execute();
	
	$jsonData = array();
	while ($array = $stmt->fetch()) {
		$jsonData[] = $array;
	}
 					   
	echo json_encode($jsonData);
	
?>


