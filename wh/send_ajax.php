<?php

include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$tb='send';

if(!isset($_POST['action'])){		
	header('Content-Type: application/json');
	echo json_encode(array('success' => false, 'message' => 'No action.'));
}else{
	switch($_POST['action']){
		//Send Adhoc
		case 'add' :				
			try{
								
				$sdNo = 'SD-'.substr(str_shuffle(MD5(microtime())), 0, 7);
				$sendDate = $_POST['sendDate'];
				$fromCode = $_POST['fromCode'];
				$toCode = $_POST['toCode'];
				$remark = $_POST['remark'];
				$refNo = $_POST['refNo'];
				
				$sendDate = str_replace('/', '-', $sendDate);
				$sendDate = date("Y-m-d",strtotime($sendDate));
				
				$sql = "INSERT INTO `".$tb."`
				(`sdNo`,`refNo`, `sendDate`, `fromCode`, `toCode`, `remark`, `statusCode`, `createTime`, `createById`) 
				VALUES
				(:sdNo, :refNo, :sendDate, :fromCode, :toCode, :remark, 'B', NOW(), :s_userId) ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->bindParam(':refNo', $refNo);
				$stmt->bindParam(':sendDate', $sendDate);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->bindParam(':remark', $remark);
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->execute();
						
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'sdNo' => $sdNo));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data insertion. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'add_item_add' :
			try{
		
				$sdNo = $_POST['sdNo'];
				$barcode = $_POST['barcode'];
				
				$pdo->beginTransaction();
					
				//Query 1: Check Status for not gen running No.
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
				
				//For Re-scan 
				$sql = "SELECT refId FROM send_scan WHERE barcodeId=:barcode AND userId=:s_userId LIMIT 1";	
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':barcode', $barcode);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count == 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Duplicate scan.'));
					exit();
				}
					
				
				$sql = "INSERT INTO  `send_detail` 
				(`prodItemId`, `sdNo`) 
				VALUES (:prodItemId, :sdNo) 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':prodItemId', $prodItemId);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				
				$tmpId = $pdo->lastInsertId(); 
				
				//Insert scanned
				$sql = "INSERT INTO  `send_scan` 
				(`barcodeId`,`refId`, `userId`) 
				VALUES (:barcode,:tmpId,:s_userId) 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':barcode', $barcode);
				$stmt->bindParam(':tmpId', $tmpId);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->execute();
				
				$pdo->commit();
				
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on data inserting. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}			
			break;	
		case 'item_delete' :
			try{				
				$pdo->beginTransaction();
				
				$id = $_POST['id'];
				
				//SQL 
				$sql = "DELETE FROM send_detail
						WHERE id=:id";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				
				//Delete scanned
				$sql = "DELETE FROM send_scan WHERE refId=:id 
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				
				$pdo->commit();
				
				//Return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data deleted'));
			}catch(Exception $e){
				$pdo->rollBack();
				
				//Return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}	
			break;
			
		default : 
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
	}//end switch action
}
//end if else check action.
?>     

