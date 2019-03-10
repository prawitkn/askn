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
		case 'search_saleOrder' :
			$search_word = $_POST['search_word'];
			
			$sql = "SELECT hdr.`soNo`, hdr.`saleDate`, hdr.`custId`, hdr.`smId`, hdr.`createTime`, hdr.`createById`, hdr.statusCode 
			, ct.name as custName, ct.addr1, ct.tel, ct.fax
			, c.name as smName 
			, d.userFullname as createByName 
			FROM `sale_header` hdr 
			left join customer ct on hdr.custId=ct.id 
			left join salesman c on hdr.smId=c.id 
			left join user d on hdr.createById=d.userId
			WHERE 1 
			AND hdr.statusCode='P' 
			AND hdr.isClose='N' 
			AND hdr.soNo like :search_word ";
			$sql .= "ORDER BY hdr.createTime DESC
			";
			//$result = mysqli_query($link, $sql);
			$stmt = $pdo->prepare($sql);
			$search_word = '%'.$search_word.'%';
			$stmt->bindParam(':search_word', $search_word);
			$stmt->execute();

			$rowCount=$stmt->rowCount();

			$jsonData = array();
			while ($array = $stmt->fetch()) {
				$jsonData[] = $array;
			}
							   
			echo json_encode(array('rowCount' => $rowCount, 'data' => json_encode($jsonData)));
			break;
		case 'add' :				
			try{
	
				$s_userId = $_SESSION['userId'];
			   
				$pickNo = $_POST['pickNo'];	
				$ppNo = 'PP-'.substr(str_shuffle(MD5(microtime())), 0, 7);
				$prepareDate = $_POST['prepareDate'];
				$remark = $_POST['remark'];
				
				$prepareDate = str_replace('/', '-', $prepareDate);
				$prepareDate = date("Y-m-d",strtotime($prepareDate));
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT pickNo FROM picking WHERE pickNo=:pickNo AND statusCode='P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Picking no. incorrect.'));
					exit();
				}	

				$sql = "INSERT INTO `prepare`
				(`ppNo`, `pickNo`, `prepareDate`, `remark`, `statusCode`, `createTime`, `createByID`) 
				VALUES (:ppNo,:pickNo,:prepareDate,:remark,'B',now(),:s_userId)
				";
						
			 	$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->bindParam(':prepareDate', $prepareDate);
				$stmt->bindParam(':remark', $remark);
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->execute();
				
				//unset($_SESSION['ppData']);
				//Delete scanned
				$sql = "DELETE FROM prepare_scan WHERE userId=:s_userId
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->execute();
				
				header('Content-Type: application/json');
			    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'ppNo'=> $ppNo ));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			exit();
			break;
		case 'add_item_add' :
			try{
		
			    $ppNo = $_POST['ppNo'];
				$barcode = $_POST['barcode'];
				
				$pdo->beginTransaction();
					
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT prodItemId 
						FROM (SELECT prodItemId, REPLACE(`barcode`, '-', '') as barcodeId 
								FROM product_item  
								 
								 ) as tmp			
						WHERE barcodeId=:barcode
						AND prodItemId IN (SELECT prodItemid FROM receive_detail WHERE statusCode='A' )
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
				$sql = "SELECT refId FROM prepare_scan WHERE barcodeId=:barcode AND userId=:s_userId LIMIT 1";	
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
						
				$sql = "INSERT INTO  `prepare_detail` 
				(`prodItemId`, `ppNo`) 
				VALUES (:prodItemId, :ppNo) 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':prodItemId', $prodItemId);
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->execute();
				
				$tmpId = $pdo->lastInsertId(); 
				
				//Insert scanned
				$sql = "INSERT INTO  `prepare_scan` 
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
		case 'add_item_delete' :
			try{
	
				//$s_userId = $_SESSION['userId']; 	
			    $id = $_POST['id'];
				
				$pdo->beginTransaction();
				
				$sql = "DELETE FROM prepare_detail WHERE id=:id 
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				
				//Delete scanned
				$sql = "DELETE FROM prepare_scan WHERE refId=:id 
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				
				$pdo->commit();
				
				header('Content-Type: application/json');
			    echo json_encode(array('success' => true, 'message' => 'Data Delete Complete.'));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
			    $pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'delete' :
			try{
			    $ppNo = $_POST['ppNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT ppNo FROM prepare WHERE ppNo=:ppNo AND statusCode<>'P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: DELETE Detail
				$sql = "DELETE FROM `prepare_detail` WHERE ppNo=:ppNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);	
				$stmt->execute();
				
				//Query 2: DELETE Header
				$sql = "DELETE FROM `prepare` WHERE ppNo=:ppNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
					
				//unset($_SESSION['ppData']);
				//Delete scanned
				$sql = "DELETE FROM prepare_scan WHERE userId=:s_userId
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->execute();
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data deleted'));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback
				$pdo->rollback();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on data deleting. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'confirm' :
			try{
			    $ppNo = $_POST['ppNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT ppNo FROM prepare WHERE ppNo=:ppNo AND statusCode='B' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: UPDATE DATA
				$sql = "UPDATE `prepare` SET statusCode='C'
						, confirmTime=now()
						, confirmById=:s_userId 
						WHERE ppNo=:ppNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);		
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data confirmed', 'ppNo' => $ppNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on data confirmation. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'reject' :
			try{
			    $ppNo = $_POST['ppNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT ppNo FROM prepare WHERE ppNo=:ppNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: UPDATE DATA
				$sql = "UPDATE `prepare` SET statusCode='B'
						WHERE ppNo=:ppNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data rejected', 'ppNo' => $ppNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on data rejection. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'approve' :
			//Check user roll.
			switch($s_userGroupCode){
				case 'it' : case 'admin' : case 'whSup' : 
					break;
				default : 
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
					exit();
			}

			try{
			    $ppNo = $_POST['ppNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT ppNo FROM prepare WHERE ppNo=:ppNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 1: GET Next Doc No.
				$year = date('Y'); $name = 'prepare'; $prefix = 'PP'.date('y'); $cur_no=1;
				$sql = "SELECT prefix, cur_no FROM doc_running WHERE year=? and name=? LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array($year, $name));
				$row_count = $stmt->rowCount();	
			    if($row_count == 0){
					$sql = "INSERT INTO doc_running (year, name, prefix, cur_no) VALUES (?,?,?,?)";
					$stmt = $pdo->prepare($sql);		
					$stmt->execute(array($year, $name, $prefix, $cur_no));
				}else{
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$prefix = $row['prefix'];
					$cur_no = (int)$row['cur_no']+1;		
				}
				$next_no = '00000'.(string)$cur_no;
				$nextNo = $prefix . substr($next_no, -6);
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE `prepare` SET statusCode='P'
						, ppNo=:nextNo
						, approveTime=now()
						, approveById=:s_userId 
						WHERE ppNo=:ppNo";
			    $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->bindParam(':ppNo', $ppNo);
			    $stmt->execute();
					
				//Query 2: UPDATE DATA
				$sql = "UPDATE prepare_detail SET ppNo=:nextNo WHERE ppNo=:ppNo ";
			    $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':ppNo', $ppNo);
			    $stmt->execute();
				
			    //UPDATE doc running.
				$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=? ";
				$stmt = $pdo->prepare($sql);		
				$stmt->execute(array($cur_no, $year, $name ));
					
				
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
				unset($_SESSION['ppData']);
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'ppNo' => $nextNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
			    $pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Approve. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'remove' :
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

