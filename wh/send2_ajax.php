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
		case 'add' :				
			try{
								
				$sdNo = 'SD-'.substr(str_shuffle(MD5(microtime())), 0, 7);
				$sendDate = $_POST['sendDate'];
				$fromCode = $_POST['fromCode'];
				$toCode = $_POST['toCode'];
				$remark = $_POST['remark'];
				
				$sendDate = str_replace('/', '-', $sendDate);
				$sendDate = date("Y-m-d",strtotime($sendDate));
				
				$sql = "INSERT INTO `".$tb."`
				(`sdNo`, `sendDate`, `fromCode`, `toCode`, `remark`, `statusCode`, `createTime`, `createByID`) 
				VALUES
				(:sdNo, :sendDate, :fromCode, :toCode, :remark, 'B', NOW(), :s_userId) ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
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
			exit();
			break;
		case 'item_add' :
			try{	
				$sdNo = $_POST['sdNo'];
				
				$pdo->beginTransaction();	
				
				if(!empty($_POST['itmId']) and isset($_POST['itmId']))
				{
					//$arrProdItems=explode(',', $prodItems);
					foreach($_POST['itmId'] as $index => $item )
					{	
						$sql = "INSERT INTO `send_detail`
						(`refNo`, `prodItemId`, `sdNo`)
						SELECT dtl.sendId, dtl.productItemId, :sdNo 
						FROM send_detail_mssql dtl 
						WHERE dtl.sendId=:sendId 
						AND dtl.productItemId=:productItemId 
						";			
						$arrItm=explode(',', $item);
						$stmt = $pdo->prepare($sql);			
						$stmt->bindParam(':sendId', $arrItm[0]);	
						$stmt->bindParam(':productItemId', $arrItm[1]);		
						$stmt->bindParam(':sdNo', $sdNo);		
						$stmt->execute();			
					}
				}
				$pdo->commit();
				
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'sdNo' => $sdNo));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Update. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors.$t));
			}
			break;
		case 'item_delete' :
			try{
				$id = $_POST['id'];

				//SQL 
				$sql = "DELETE FROM send_detail
						WHERE id=:id";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->execute();

				//Return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data deleted'));
			}catch(Exception $e){
				//Return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}	
			break;
		case 'edit' :
			$id = $_POST['id'];
			$custId = $_POST['custId'];
			$code = $_POST['code'];
			$name = $_POST['name'];
			$addr1 = $_POST['addr1'];
			$addr2 = $_POST['addr2'];
			$addr3 = $_POST['addr3'];
			$zipcode = $_POST['zipcode'];
			$countryName = $_POST['countryName'];
			$locationCode = $_POST['locationCode'];
			$marketCode = $_POST['marketCode'];
			$contact = $_POST['contact'];
			$contactPosition = $_POST['contactPosition'];
			$email = $_POST['email'];
			$tel = $_POST['tel']; 
			$fax = $_POST['fax']; 
			$smId = $_POST['smId']; 
			$smAdmId = (isset($_POST['smAdmId'])? $_POST['smAdmId'] : 0 );//if because column datatype = int
			$statusCode = (isset($_POST['statusCode'])? $_POST['statusCode'] : 'I' );
							
			$sql = "UPDATE `".$tb."` SET `custId`=:custId, `code`=:code, `name`=:name, `addr1`=:addr1, `addr2`=:addr2
			, `addr3`=:addr3, `zipcode`=:zipcode, `countryName`=:countryName, `locationCode`=:locationCode, `marketCode`=:marketCode
			, `contact`=:contact, `contactPosition`=:contactPosition, `email`=:email, `tel`=:tel, `fax`=:fax, `smId`=:smId, `smAdmId`=:smAdmId
			, `statusCode`=:statusCode 				
			WHERE id=:id 
			";	
			$stmt = $pdo->prepare($sql);	
			$stmt->bindParam(':custId', $custId);
			$stmt->bindParam(':code', $code);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':addr1', $addr1);
			$stmt->bindParam(':addr2', $addr2);
			$stmt->bindParam(':addr3', $addr3);
			$stmt->bindParam(':zipcode', $zipcode);
			$stmt->bindParam(':countryName', $countryName);
			$stmt->bindParam(':locationCode', $locationCode);
			$stmt->bindParam(':marketCode', $marketCode);
			$stmt->bindParam(':contact', $contact);
			$stmt->bindParam(':contactPosition', $contactPosition);
			
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':tel', $tel);
			$stmt->bindParam(':fax', $fax);
			
			
			$stmt->bindParam(':smId', $smId);
			$stmt->bindParam(':smAdmId', $smAdmId);
			$stmt->bindParam(':statusCode', $statusCode);
			$stmt->bindParam(':id', $id);
			if ($stmt->execute()) {
				  header('Content-Type: application/json');
				  echo json_encode(array('success' => true, 'message' => 'Data Updated Complete.'));
			   } else {
				  header('Content-Type: application/json');
				  $errors = "Error on Data Update. Please try new data. " . $pdo->errorInfo();
				  echo json_encode(array('success' => false, 'message' => $errors));
			}	
			break;
		case 'mapping' :
			try{	
				$sdNo = $_POST['sdNo'];
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM send WHERE sdNo=:sdNo AND statusCode<>'P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE product_item itm
				INNER JOIN product_mapping pm on itm.prodId=pm.invProdId
				SET itm.prodCodeId=pm.wmsProdId 
				WHERE itm.prodItemId IN (SELECT dtl.prodItemId FROM send_detail dtl WHERE dtl.sdNo=:sdNo) 
					";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data mapping completed.', 'sdNo' => $sdNo, 'rowCount' => $stmt->rowCount()));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data mapping. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'delete' :
			try{
				$sdNo = $_POST['sdNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT sdNo FROM send WHERE sdNo=:sdNo AND statusCode<>'P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				$hdr = $stmt->fetch();	
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: DELETE Detail
				$sql = "DELETE FROM `send_detail` WHERE sdNo=:sdNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);	
				$stmt->execute();
				
				//Query 2: DELETE Header
				$sql = "DELETE FROM `send` WHERE sdNo=:sdNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);	
				$stmt->execute();
						
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
					
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Deleted'));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback
				$pdo->rollback();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Deleting. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'confirm' :
			try{	
				//$session_userID=$_SESSION['userID'];
				
				$sdNo = $_POST['sdNo'];

				//We start our transaction.
				$pdo->beginTransaction();	
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM send WHERE sdNo=:sdNo AND statusCode='B' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 1: Check is settle all product Item 	
				$sql = "SELECT dtl.id FROM send_detail dtl INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId AND (itm.prodCodeId IS NULL OR itm.prodCodeId='') WHERE sdNo=:sdNo ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 0){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Some item is not settle product code yet.'));
					exit();
				}
				
				//Query 2: UPDATE DATA
				$sql = "UPDATE send SET statusCode='C'   
					, confirmTime=now()
					, confirmById=?
					WHERE sdNo=? ";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array(	
						$s_userId,
						$sdNo	
					)
				);
					
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Confirmed'));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Confirmation. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'reject' :
			try{	
				$sdNo = $_POST['sdNo'];
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM send WHERE sdNo=:sdNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE send SET statusCode='B'
						WHERE sdNo=:sdNo
						AND statusCode='C' 
					";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Rejected'));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Rejection. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'approve' :
			//Check user roll.
			switch($s_userGroupCode){
				case 'it' : case 'admin' : case 'whSup' : case 'pdSup' :
					break;
				default : 
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
					exit();
			}

			$sdNo = $_POST['sdNo'];

			//We will need to wrap our queries inside a TRY / CATCH block.
			//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
			try{
				//We start our transaction.
				$pdo->beginTransaction();
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM send WHERE sdNo=:sdNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				$hdr = $stmt->fetch();
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				$fromCode = $hdr['fromCode'];
				$toCode = $hdr['toCode'];
				
				//Query 1: GET Next Doc No.
				$year = date('Y'); $name = 'send'; $prefix = 'RM'.date('y').$fromCode; $cur_no=1;
				$sql = "SELECT prefix, cur_no FROM doc_running WHERE year=? and name=?  and prefix=? LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array($year, $name, $prefix));
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
				$noNext = $prefix . substr($next_no, -5);
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE send SET statusCode='P'
				, sdNo=:noNext  
				, approveTime=now()
				, approveById=:approveById
				WHERE sdNo=:sdNo  
				AND statusCode='C' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':noNext', $noNext);
				$stmt->bindParam(':approveById', $s_userId);
				$stmt->bindParam(':sdNo', $sdNo);
				$stmt->execute();
					
				//Query 3: UPDATE DATA
				$sql = "UPDATE send_detail SET sdNo=? WHERE sdNo=? ";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array($noNext,$sdNo));
				
				//Query 4:  UPDATE doc running.
				$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
				$stmt = $pdo->prepare($sql);		
				$stmt->execute(array($cur_no, $year, $name));	
				
				
				
				
				//Query 5: UPDATE STK BAl sloc from 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM send_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE sdNo=:sdNo GROUP BY itm.prodCodeId) as s
				SET sb.send=sb.send+s.sumQty
				, sb.balance=sb.balance-s.sumQty 
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:fromCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $noNext);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->execute();
					
				//Query 6: INSERT STK BAl sloc from 
				$sql = "INSERT INTO stk_bal (prodId, sloc, send, balance) 
				SELECT itm.prodCodeId, :fromCode, SUM(itm.qty), -1*SUM(itm.qty) 
				FROM send_detail sd
				INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
				WHERE sd.sdNo=:sdNo 
				AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:fromCode2)
				GROUP BY itm.prodCodeId
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $noNext);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->bindParam(':fromCode2', $fromCode);
				$stmt->execute();
				
				//Query 5: UPDATE STK BAl sloc to 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM send_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE sdNo=:sdNo GROUP BY itm.prodCodeId) as s
				SET sb.onway=sb.onway+s.sumQty
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:toCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $noNext);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->execute();
				
				//Query 6: INSERT STK BAl sloc to 
				$sql = "INSERT INTO stk_bal (prodId, sloc, onway) 
						SELECT itm.prodCodeId, :toCode, SUM(itm.qty) 
						FROM send_detail sd 
						INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
						WHERE sd.sdNo=:sdNo 
						AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
						GROUP BY itm.prodCodeId
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $noNext);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->bindParam(':toCode2', $toCode);
				$stmt->execute();
				
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Approved', 'sdNo' => $noNext));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Approval. Please try again. " . $e->getMessage();
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

