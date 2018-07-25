<?php

include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$tb='rt';

if(!isset($_POST['action'])){		
	header('Content-Type: application/json');
	echo json_encode(array('success' => false, 'message' => 'No action.'));
}else{
	switch($_POST['action']){
		case 'add' :
			try{			
				$s_userId = $_SESSION['userId'];
				$rtNo = 'RT-'.substr(str_shuffle(MD5(microtime())), 0, 7);
				//$rcNo = $_POST['rcNo'];
				$returnDate = $_POST['returnDate'];
				$refNo = $_POST['refNo'];
				$remark = $_POST['remark'];
				
				$returnDate = str_replace('/', '-', $returnDate);
				$returnDate = date("Y-m-d",strtotime($returnDate));
				
				$sql = "INSERT INTO `rt`
				(`rtNo`, `returnDate`, `refNo`, `fromCode`, `toCode`, `remark`,  `statusCode`, `createTime`, `createById`) 
				SELECT :rtNo,:returnDate,:refNo,rc.toCode,rc.fromCode,:remark,'B',now(),:s_userId
				FROM receive rc 
				WHERE rc.rcNo=:rcNo 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->bindParam(':returnDate', $returnDate);
				$stmt->bindParam(':refNo', $refNo);
				$stmt->bindParam(':remark', $remark);
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->bindParam(':rcNo', $refNo);
				$stmt->execute();
						
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'rtNo' => $rtNo));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data insertion. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}			
			break;
		case 'item_add' :
			try{	
				$rtNo = $_POST['rtNo'];
				
				$pdo->beginTransaction();	
				
				if(!empty($_POST['itmId']) and isset($_POST['itmId']))
				{
					//$arrProdItems=explode(',', $prodItems);
					foreach($_POST['itmId'] as $index => $item )
					{	
						$sql = "INSERT INTO `rt_detail`
						(`prodItemId`
						, `returnReasonCode`, `returnReasonRemark`, `rtNo`)
						SELECT rc.`prodItemId`
						,:returnReasonCode, :returnReasonRemark, :rtNo 
						FROM receive_detail rc 
						WHERE rc.id=:id 
						AND rc.prodItemId NOT IN (SELECT x.prodItemId FROM rt_detail x WHERE x.rtNo=:rtNo2)
						";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':returnReasonCode', $_POST['returnReasonCode'][$index]);	
						$stmt->bindParam(':returnReasonRemark', $_POST['returnReasonRemark'][$index]);	
						$stmt->bindParam(':rtNo', $rtNo);	
						$stmt->bindParam(':rtNo2', $rtNo);	
						$stmt->bindParam(':id', $item);		
						$stmt->execute();											
					}
				}
				$pdo->commit();
				
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'rtNo' => $rtNo));
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
				$sql = "DELETE FROM rt_detail
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
				$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}		
			break;
		case 'delete' :
			try{
				$rtNo = $_POST['rtNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT rtNo FROM rt WHERE rtNo=:rtNo AND statusCode<>'P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
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
				$sql = "DELETE FROM `rt_detail` WHERE rtNo=:rtNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);	
				$stmt->execute();
				
				//Query 2: DELETE Header
				$sql = "DELETE FROM `rt` WHERE rtNo=:rtNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);	
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
				$rtNo = $_POST['rtNo'];

				//We start our transaction.
				$pdo->beginTransaction();	
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM rt WHERE rtNo=:rtNo AND statusCode='B' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 2: UPDATE DATA
				$sql = "UPDATE rt SET statusCode='C'   
					, confirmTime=now()
					, confirmById=?
					WHERE rtNo=? ";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array(	
						$s_userId,
						$rtNo	
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
				$rtNo = $_POST['rtNo'];
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM rt WHERE rtNo=:rtNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE rt SET statusCode='B'
						WHERE rtNo=:rtNo
						AND statusCode='C' 
					";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
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

			//We will need to wrap our queries inside a TRY / CATCH block.
			//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
			try{
				$rtNo = $_POST['rtNo'];
				//We start our transaction.
				$pdo->beginTransaction();
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM rt WHERE rtNo=:rtNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				$hdr=$stmt->fetch();
				$fromCode = $hdr['fromCode'];
				$toCode = $hdr['toCode'];
				
				//Query 1: GET Next Doc No.
				$year = date('Y'); $name = 'return'; $prefix = 'RT'.date('y').$fromCode; $cur_no=1;
				$sql = "SELECT prefix, cur_no FROM doc_running WHERE year=? and name=?  and prefix=?  LIMIT 1";
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
				$sql = "UPDATE rt SET statusCode='P'
				, rtNo=:noNext  
				, approveTime=now()
				, approveById=:approveById
				WHERE rtNo=:rtNo  
				AND statusCode='C' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':noNext', $noNext);
				$stmt->bindParam(':approveById', $s_userId);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->execute();
					
				//Query 3: UPDATE DATA
				$sql = "UPDATE rt_detail SET rtNo=? WHERE rtNo=? ";
				$stmt = $pdo->prepare($sql);
				$stmt->execute(array($noNext,$rtNo));
				
				//Query 4:  UPDATE doc running.
				$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=? and prefix=?";
				$stmt = $pdo->prepare($sql);		
				$stmt->execute(array($cur_no, $year, $name, $prefix));
				
				
				
				
				
				
				
				//Query 5: UPDATE STK BAl sloc from 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM rt_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE rtNo=:rtNo GROUP BY itm.prodCodeId) as s
				SET sb.send=sb.send+s.sumQty
				, sb.balance=sb.balance-s.sumQty 
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:fromCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $noNext);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->execute();
					
				//Query 6: INSERT STK BAl sloc from 
				$sql = "INSERT INTO stk_bal (prodId, sloc, send, balance) 
				SELECT itm.prodCodeId, :fromCode, SUM(itm.qty), -1*SUM(itm.qty) 
				FROM rt_detail sd
				INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
				WHERE sd.rtNo=:rtNo 
				AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:fromCode2)
				GROUP BY itm.prodCodeId
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $noNext);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->bindParam(':fromCode2', $fromCode);
				$stmt->execute();
				
				//Query 5: UPDATE STK BAl sloc to 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM rt_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE rtNo=:rtNo GROUP BY itm.prodCodeId) as s
				SET sb.onway=sb.onway+s.sumQty
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:toCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $noNext);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->execute();
				
				//Query 6: INSERT STK BAl sloc to 
				$sql = "INSERT INTO stk_bal (prodId, sloc, onway) 
						SELECT itm.prodCodeId, :toCode, SUM(itm.qty) 
						FROM rt_detail sd 
						INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
						WHERE sd.rtNo=:rtNo 
						AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
						GROUP BY itm.prodCodeId
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $noNext);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->bindParam(':toCode2', $toCode);
				$stmt->execute();
									
				
				//Query 3: UPDATE Shelf
				$sql = "DELETE wsi 
				FROM wh_shelf_map_item wsi
				INNER JOIN receive_detail rcDtl ON rcDtl.id=wsi.recvProdId  
				INNER JOIN rt_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.rtNo=:rtNo
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $noNext);
				$stmt->execute();
				
				//Query 3: UPDATE Receive Detail 
				$sql = "UPDATE receive_detail rcDtl 
				INNER JOIN rt_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.rtNo=:rtNo
				SET rcDtl.statusCode='R' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $noNext);
				$stmt->execute();
								
						
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'rtNo' => $noNext));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data approval. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'remove' :
			//Check user roll.
			switch($s_userGroupCode){
				case 'admin' : 
					break;
				default : 
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
					exit();
			}

			//We will need to wrap our queries inside a TRY / CATCH block.
			//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
			try{
				$rtNo = $_POST['rtNo'];
				//We start our transaction.
				$pdo->beginTransaction();
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM rt WHERE rtNo=:rtNo AND statusCode='P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
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
					echo json_encode(array('success' => false, 'message' => 'Return has been received.'));
					exit();
				}			
				$fromCode = $hdr['fromCode'];
				$toCode = $hdr['toCode'];
											
				//Query 1: UPDATE DATA
				$sql = "UPDATE rt SET statusCode='X'
				, updateTime=now()
				, updateById=:updateById
				WHERE rtNo=:rtNo  
				AND statusCode='P' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':updateById', $s_userId);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->execute();
						
				
				
				
				
				//Query 5: UPDATE STK BAl sloc from 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)*-1  as sumQty
					   FROM rt_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE rtNo=:rtNo GROUP BY itm.prodCodeId) as s
				SET sb.send=sb.send-s.sumQty
				, sb.balance=sb.balance+s.sumQty 
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:fromCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->execute();
					
				//Query 6: INSERT STK BAl sloc from 
				$sql = "INSERT INTO stk_bal (prodId, sloc, send, balance) 
				SELECT itm.prodCodeId, :fromCode, -1*SUM(itm.qty), SUM(itm.qty) 
				FROM rt_detail sd
				INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
				WHERE sd.rtNo=:rtNo 
				AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:fromCode2)
				GROUP BY itm.prodCodeId
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->bindParam(':fromCode', $fromCode);
				$stmt->bindParam(':fromCode2', $fromCode);
				$stmt->execute();
				
				//Query 5: UPDATE STK BAl sloc to 
				$sql = "		
				UPDATE stk_bal sb,
				( SELECT itm.prodCodeId, sum(itm.qty)  as sumQty
					   FROM rt_detail dtl
					   INNER JOIN product_item itm ON itm.prodItemId=dtl.prodItemId 
					   WHERE rtNo=:rtNo GROUP BY itm.prodCodeId) as s
				SET sb.onway=sb.onway-s.sumQty
				WHERE sb.prodId=s.prodCodeId
				AND sb.sloc=:toCode
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->execute();
				
				//Query 6: INSERT STK BAl sloc to 
				$sql = "INSERT INTO stk_bal (prodId, sloc, onway) 
						SELECT itm.prodCodeId, :toCode, -1*SUM(itm.qty) 
						FROM rt_detail sd 
						INNER JOIN product_item itm ON itm.prodItemId=sd.prodItemId 
						WHERE sd.rtNo=:rtNo 
						AND itm.prodCodeId NOT IN (SELECT sb2.prodId FROM stk_bal sb2 WHERE sb2.sloc=:toCode2)
						GROUP BY itm.prodCodeId
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->bindParam(':toCode', $toCode);
				$stmt->bindParam(':toCode2', $toCode);
				$stmt->execute();
									
				
				//Query 3: UPDATE Shelf
				$sql = "DELETE wsi 
				FROM wh_shelf_map_item wsi
				INNER JOIN receive_detail rcDtl ON rcDtl.id=wsi.recvProdId  
				INNER JOIN rt_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.rtNo=:rtNo
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->execute();
				
				//Query 3: UPDATE Receive Detail 
				$sql = "UPDATE receive_detail rcDtl 
				INNER JOIN rt_detail rtDtl ON rtDtl.prodItemId=rcDtl.prodItemId AND rtDtl.rtNo=:rtNo
				SET rcDtl.statusCode='R' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':rtNo', $rtNo);
				$stmt->execute();
								
						
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'rtNo' => $rtNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data approval. Please try again. " . $e->getMessage();
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

