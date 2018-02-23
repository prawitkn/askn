<?php
	include '../db/database_sqlsrv.php';
	include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/

	//$search_fromDate = $_POST['fromDate'];
	
	
	$sql = "SELECT TOP 100 [SendID]
      ,[SendNo]
      ,[SendNum]
      ,[IssueDate]
      ,[Quantity]
      ,[IsCustomer]
      ,[CustomerID]
      ,[OnAddress]
      ,[Address]
      ,[Telephone]
      ,[Fax] FROM send  
  ";
  /*switch($s_userGroupCode){ 
		case 'it' : 
		case 'admin' : 
			break;
		case 'warehouse' :
		case 'production' :
			$sql .= "AND send.toCode=$s_userDept ";
			break;
		default :
	}*/	
	$sql .= "ORDER BY send.IssueDate DESC
	";
//echo $sql;
$msResult = sqlsrv_query($ssConn, $sql);
			
			//echo $msResult;
			
			
	//$result = mysqli_query($link, $sql);
	//$stmt = $pdo->prepare($sql);
	//$search_fullname = '%'.$search_fullname.'%';
	//$stmt->bindParam(':search_word', $search_fullname);
	//$stmt->execute();

	$jsonData = array();
	while ($array = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC)) {
		$jsonData[] = $array; 
		//echo json_encode($array)."</br>";
		
	}
	echo json_encode($jsonData);
	//echo json_encode($jsonData);
	sqlsrv_free_stmt( $msResult);
	sqlsrv_close( $ssConn);
?>


