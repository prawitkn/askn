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
				$sdNo = $_POST['sdNo'];	
				//$refNo = $_POST['refNo'];	
				$receiveDate = $_POST['receiveDate'];	
				$remark = $_POST['remark'];	
				
				//$receiveDate = to_mysql_date($receiveDate);
				$receiveDate = str_replace('/', '-', $receiveDate);
				$receiveDate = date("Y-m-d",strtotime($receiveDate));
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT sdNo FROM send WHERE sdNo=:sdNo AND statusCode='P' LIMIT 1";
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
				
				$rcNo = 'RS-'.substr(str_shuffle(MD5(microtime())), 0, 7);
				//Query 1: DELETE Detail
				$sql = "INSERT INTO `receive`(`rcNo`, `receiveDate`, `type`, `fromCode`, `toCode`, `remark`, `sdNo`, `statusCode`, `createByID`) 
				SELECT :rcNo,:receiveDate, 'S',`fromCode`, `toCode`,:remark,`sdNo`, 'B',:s_userId  
				FROM send 
				WHERE sdNo=:sdNo 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);	
				$stmt->bindParam(':receiveDate', $receiveDate);
				$stmt->bindParam(':remark', $remark);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->bindParam(':sdNo', $sdNo);		
				$stmt->execute();
				
				//INsert Detail
				$sql = "INSERT INTO `receive_detail`(`prodItemId`, `statusCode`, `rcNo`) 	 	
				SELECT `prodItemId`, 'A', :rcNo 
				FROM send_detail  
				WHERE sdNo=:sdNo 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->bindParam(':sdNo', $sdNo);		
				$stmt->execute();
							
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
					
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data deleted', 'rcNo' => $rcNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback
				$pdo->rollback();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
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
		case 'delete' :
			try{
				$rcNo = $_POST['rcNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT rcNo FROM receive WHERE rcNo=:rcNo AND statusCode<>'P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
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
				$sql = "DELETE FROM `receive_detail` WHERE rcNo=:rcNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);	
				$stmt->execute();
				
				//Query 2: DELETE Header
				$sql = "DELETE FROM `receive` WHERE rcNo=:rcNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);	
				$stmt->execute();
						
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
					
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
				$rcNo = $_POST['rcNo'];

				//We start our transaction.
				$pdo->beginTransaction();	
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM receive WHERE rcNo=:rcNo AND statusCode='B' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 2: UPDATE DATA
				$sql = "UPDATE receive SET statusCode='C'   
					, confirmTime=now()
					, confirmById=?
					WHERE rcNo=? ";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array(	
						$s_userId,
						$rcNo	
					)
				);
					
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data confirmed'));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on data confirmation. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'reject' :
			try{	
				$rcNo = $_POST['rcNo'];
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM receive WHERE rcNo=:rcNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE receive SET statusCode='B'
						WHERE rcNo=:rcNo
						AND statusCode='C' 
					";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->execute();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data rejected'));
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
				case 'it' : case 'admin' : case 'whSup' : case 'pdSup' : 
					break;
				default : 
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
					exit();
			}

			$rcNo = $_POST['rcNo'];

			//We will need to wrap our queries inside a TRY / CATCH block.
			//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
			try{
				//We start our transaction.
				$pdo->beginTransaction();
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM receive WHERE rcNo=:rcNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				$row = $stmt->fetch();
				$toCode = $row['toCode'];
				$sdNo = $row['sdNo'];
				
				//Query 1: GET Next Doc No. RS-D-YY ; D=department no.
				$year = date('Y'); $name = 'receive'; $prefix = 'RS'.date('y').$toCode; $cur_no=1;
				$sql = "SELECT prefix, cur_no FROM doc_running WHERE year=? and name=? and prefix=? LIMIT 1";
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
				$sql = "UPDATE receive SET statusCode='P'
				, rcNo=:noNext  
				, approveTime=now()
				, approveById=:approveById
				WHERE rcNo=:rcNo  
				AND statusCode='C' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':noNext', $noNext);
				$stmt->bindParam(':approveById', $s_userId);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->execute();
					
				//Query 3: UPDATE DATA
				$sql = "UPDATE receive_detail SET rcNo=? WHERE rcNo=? ";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array($noNext,$rcNo));
				
				//Query 3: UPDATE Send
				$sql = "UPDATE send SET rcNo=? WHERE sdNo=? ";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array($noNext,$sdNo));
				
				//Query 4:  UPDATE doc running.
				$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
				$stmt = $pdo->prepare($sql);		
				$stmt->execute(array($cur_no, $year, $name));
				
				
				
				
				//Query 5: UPDATE STK BAl sloc to 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM receive_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE rcNo=:rcNo GROUP BY itm.prodCodeId) as s
				SET sb.onway=sb.onway-s.sumQty
				, sb.receive=sb.receive+s.sumQty
				, sb.balance=sb.balance+s.sumQty 
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:toCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $noNext);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->execute();
					
				//Query 6: INSERT STK BAl sloc to 
				$sql = "INSERT INTO stk_bal (prodId, sloc, onway, receive, balance) 
				SELECT itm.prodCodeId, :toCode, -1*SUM(itm.qty), SUM(itm.qty), -1*SUM(itm.qty) 
				FROM receive_detail dtl
				INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
				WHERE dtl.rcNo=:rcNo 
				AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
				GROUP BY itm.prodCodeId
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $noNext);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->bindParam(':toCode2', $toCode);
				$stmt->execute();
								
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'rcNo' => $noNext));	
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
			break;
		case 'remove' :
			//Check user roll.
			switch($s_userGroupCode){
				case 'admin' : case 'whSup' : case 'pdSup' : 
					break;
				default : 
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
					exit();
			}

			$rcNo = $_POST['rcNo'];

			//We will need to wrap our queries inside a TRY / CATCH block.
			//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
			try{
				//We start our transaction.
				$pdo->beginTransaction();
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM receive WHERE rcNo=:rcNo AND statusCode='P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}				
				$hdr = $stmt->fetch();
				if(trim($hdr['rcNo'])<>"" ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Sending has been received.'));
					exit();
				}			
				$fromCode = $hdr['fromCode'];
				$toCode = $hdr['toCode'];
							
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE receive SET statusCode='X'
				, updateTime=now()
				, updateById=:updateById
				WHERE rcNo=:rcNo  
				AND statusCode='P' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':updateById', $s_userId);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->execute();
								
				//Query 5: UPDATE STK BAl sloc from 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM receive_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE rcNo=:rcNo GROUP BY itm.prodCodeId) as s
				SET sb.send=sb.send-s.sumQty
				, sb.balance=sb.balance+s.sumQty 
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:fromCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->execute();
					
				//Query 6: INSERT STK BAl sloc from 
				$sql = "INSERT INTO stk_bal (prodId, sloc, send, balance) 
				SELECT itm.prodCodeId, :fromCode, -1*SUM(itm.qty), SUM(itm.qty) 
				FROM receive_detail sd
				INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
				WHERE sd.rcNo=:rcNo 
				AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:fromCode2)
				GROUP BY itm.prodCodeId
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->bindParam(':fromCode2', $fromCode);
				$stmt->execute();
				
				//Query 5: UPDATE STK BAl sloc to 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM receive_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE rcNo=:rcNo GROUP BY itm.prodCodeId) as s
				SET sb.onway=sb.onway-s.sumQty
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:toCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->execute();
				
				//Query 6: INSERT STK BAl sloc to 
				$sql = "INSERT INTO stk_bal (prodId, sloc, onway) 
						SELECT itm.prodCodeId, :toCode, -1*SUM(itm.qty) 
						FROM receive_detail sd 
						INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
						WHERE sd.rcNo=:rcNo 
						AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
						GROUP BY itm.prodCodeId
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rcNo', $rcNo);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->bindParam(':toCode2', $toCode);
				$stmt->execute();
				
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Removed', 'rcNo' => $rcNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Remove. Please try again. " . $e->getMessage();
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

