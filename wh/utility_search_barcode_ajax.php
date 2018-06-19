<?php

include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$tb='prepare';

if(!isset($_POST['action'])){		
	header('Content-Type: application/json');
	echo json_encode(array('success' => false, 'message' => 'No action.'));
}else{
	switch($_POST['action']){
		case 'get_barcode' :
			$barcode=(isset($_GET['barcode'])?$_GET['barcode']:0);
			$sql = "SELECT prodItemId 
						FROM (SELECT prodItemId, REPLACE(`barcode`, '-', '') as barcodeId 
								FROM product_item  
								 
								 ) as tmp			
						WHERE barcodeId=:barcode
						LIMIT 1 ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':barcode', $barcode);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Not found.'));
					exit();
				}
				$row=$stmt->fetch();
				$prodItemId = $row['prodItemId'];
				
			header('Content-Type: application/json');
			$errors = "Do Nothing.";
			echo json_encode(array('success' => false, 'message' => $errors));
			break;
		default : 
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
	}//end switch action
}
//end if else check action.
?>     

