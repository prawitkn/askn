<?php

include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$tb='delivery';

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
	  
				$doNo = 'DO-'.substr(str_shuffle(MD5(microtime())), 0, 7);
				$ppNo = $_POST['ppNo'];
				$deliveryDate = $_POST['deliveryDate'];
				$remark = $_POST['remark'];
				
				$deliveryDate = str_replace('/', '-', $deliveryDate);
				$deliveryDate = date("Y-m-d",strtotime($deliveryDate));

				$pdo->beginTransaction();
				
				$sql = "INSERT INTO `delivery_header`
				(`doNo`, `soNo`, `ppNo`, `deliveryDate`, `custId`, `shipToId`, `smId`, `remark`, `statusCode`, `createTime`, `createById`) 
				SELECT :doNo,oh.soNo,pp.ppNo,:deliveryDate,oh.custId,oh.shipToId,oh.smId,:remark,'B',now(),:s_userId 
				FROM sale_header oh
				INNER JOIN picking pk on pk.soNo=oh.soNo 
				INNER JOIN prepare pp on pp.pickNo=pk.pickNo 
				WHERE 1
				AND pp.ppNo=:ppNo 
						";
			 	$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->bindParam(':deliveryDate', $deliveryDate);
				$stmt->bindParam(':remark', $remark);
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->execute();
				
				//INsert Detail
				/*$sql = "INSERT INTO `delivery_detail`(`prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`
				, `NW`, `GW`, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`, `doNo`) 	 	
				SELECT `prodItemId`, `prodId`, `prodCode`, `barcode`, `issueDate`, `machineId`, `seqNo`
				, `NW`, `GW`, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`,:doNo 
				FROM prepare_detail  
				WHERE ppNo=:ppNo 
				";*/
				$sql = "INSERT INTO `delivery_detail`(`prodItemId`, `doNo`) 	 	
				SELECT `prodItemId`,:doNo 
				FROM prepare_detail  
				WHERE ppNo=:ppNo 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->bindParam(':ppNo', $ppNo);		
				$stmt->execute();
				
				//`prodId`, `itemCount`, `qty`, `remark`, `doNo`
				$sql = "INSERT INTO `delivery_prod`(`prodId`, `itemCount`, `qty`, `remark`, `doNo`) 	 	
				SELECT itm.prodCodeId, COUNT(*), SUM(itm.qty), '',:doNo 
				FROM prepare_detail pDtl 
				LEFT JOIN product_item itm ON itm.prodItemId=pDtl.prodItemId 
				WHERE ppNo=:ppNo 
				GROUP BY pDtl.prodId
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->bindParam(':ppNo', $ppNo);		
				$stmt->execute();
				
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
						
				header('Content-Type: application/json');
			    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'doNo'=>$doNo ));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback
				$pdo->rollback();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}		
			break;
		case 'add_item_submit' :
			try{
	
				$s_userId = $_SESSION['userId']; 
				$doNo = $_POST['doNo']; 
				
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT doNo FROM delivery_header WHERE doNo=:doNo AND statusCode='B' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	

				if(!empty($_POST['id']) and isset($_POST['id']))
			    {
					//$arrProdItems=explode(',', $prodItems);
			        foreach($_POST['id'] as $index => $item )
			        {	
						$sql = "UPDATE `delivery_prod` SET remark=:remark WHERE id=:id 
						";						
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':remark', $_POST['remark'][$index]);	
						$stmt->bindParam(':id', $item);		
						$stmt->execute();			
			        }
			    }												
				
					
				//Query 1: UPDATE DATA
				$sql = "UPDATE `delivery_header` SET statusCode='C'
						, confirmTime=now()
						, confirmById=:s_userId 
						WHERE doNo=:doNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);		
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
				header('Content-Type: application/json');
			    echo json_encode(array('success' => true, 'message' => 'Data Submit Complete.', 'doNo' => $doNo));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
			    //Rollback the transaction.
			    $pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Submit. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'delete' :
			try{
			    $doNo = $_POST['doNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT doNo FROM delivery_header WHERE doNo=:doNo AND statusCode<>'P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
				
				//Query 1: DELETE Detail
				$sql = "DELETE FROM `delivery_prod` WHERE doNo=:doNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);	
				$stmt->execute();
				
				//Query 1: DELETE Detail
				$sql = "DELETE FROM `delivery_detail` WHERE doNo=:doNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);	
				$stmt->execute();
				
				//Query 2: DELETE Header
				$sql = "DELETE FROM `delivery_header` WHERE doNo=:doNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);	
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
				$errors = "Error on Data Reject. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'confirm' :
			try{
			    $doNo = $_POST['doNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT doNo FROM delivery_header WHERE doNo=:doNo AND statusCode='B' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: UPDATE DATA
				$sql = "UPDATE `delivery_header` SET statusCode='C'
						, confirmTime=now()
						, confirmById=:s_userId 
						WHERE doNo=:doNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);		
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'doNo' => $doNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'reject' :
			try{
			    $doNo = $_POST['doNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT doNo FROM delivery_header WHERE doNo=:doNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: UPDATE DATA
				$sql = "UPDATE `delivery_header` SET statusCode='B'
						WHERE doNo=:doNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'doNo' => $doNo));	
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Reject. Please try again. " . $e->getMessage();
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
			    $doNo = $_POST['doNo'];	
				$soNo = $_POST['soNo'];
				$isClose = $_POST['isClose'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT do.doNo, pp.pickNo FROM delivery_header do
				INNER JOIN prepare pp ON pp.ppNo=do.ppNo 
				WHERE do.doNo=:doNo AND do.statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				$row=$stmt->fetch();
				$pickNo=$row['pickNo'];
				
				//Query 1: GET Next Doc No.
				$year = date('Y'); $name = 'delivery'; $prefix = 'DO'.date('y'); $cur_no=1;
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
				
				$sql = "UPDATE picking a SET a.isFinish='Y' WHERE pickNo=:pickNo ";
			    $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
			    $stmt->execute();

				//Query 2: UPDATE DATA
				$sql = "UPDATE receive_detail rDtl 
						INNER JOIN delivery_detail dDtl ON dDtl.prodItemId=rDtl.prodItemId AND dDtl.doNo=:doNo 
						SET rDtl.statusCode='X' ";
			    $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
			    $stmt->execute();
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE `delivery_header` SET statusCode='P'
						, doNo=:nextNo
						, approveTime=now()
						, approveById=:s_userId 
						WHERE doNo=:doNo";
			    $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->bindParam(':doNo', $doNo);
			    $stmt->execute();
					
				//Query 2: UPDATE DATA
				$sql = "UPDATE delivery_detail SET doNo=:nextNo WHERE doNo=:doNo ";
			    $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':doNo', $doNo);
			    $stmt->execute();
				
				//Query 2: UPDATE DATA
				$sql = "UPDATE delivery_prod SET doNo=:nextNo WHERE doNo=:doNo ";
			    $stmt = $pdo->prepare($sql);
				$stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':doNo', $doNo);
			    $stmt->execute();
				
			    //UPDATE doc running.
				$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
				$stmt = $pdo->prepare($sql);		
				$stmt->execute(array($cur_no, $year, $name));
				
				//Close Sales Order.
				if($isClose=='Yes'){
					$sql = "UPDATE sale_header SET isClose='Y' WHERE soNo=:soNo ";
					$stmt = $pdo->prepare($sql);		
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
				}
				
				//Query 5: UPDATE STK BAl
				$sql = "UPDATE stk_bal sb		
				SET sb.delivery = sb.delivery+(SELECT SUM(dd.qty) FROM delivery_detail dd 	
												INNER JOIN product_item itm ON dd.prodItemId=itm.prodItemId 	 
												WHERE itm.prodCodeId=sb.prodId
												AND dd.doNo=:nextNo) 
				, sb.sales = sb.sales-(SELECT SUM(dd.qty) FROM delivery_detail dd 	
												INNER JOIN product_item itm ON dd.prodItemId=itm.prodItemId 	 
												WHERE itm.prodCodeId=sb.prodId
												AND dd.doNo=:nextNo2) 
				, sb.balance = sb.balance-(SELECT SUM(dd.qty) FROM delivery_detail dd 	
												INNER JOIN product_item itm ON dd.prodItemId=itm.prodItemId 	 
												WHERE itm.prodCodeId=sb.prodId
												AND dd.doNo=:nextNo3) 
				AND sb.sloc='8' 
				";
			    $stmt = $pdo->prepare($sql);
			    $stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':nextNo2', $nextNo);
				$stmt->bindParam(':nextNo3', $nextNo);
			    $stmt->execute();
				
				//Query 6: INSERT STK BAl
				$sql = "INSERT INTO stk_bal (prodId, sloc, delivery, sales, balance) 
				SELECT dd.prodId,'8', SUM(dd.qty), SUM(-1*dd.qty), SUM(-1*dd.qty)  FROM delivery_detail dd 
				WHERE dd.doNo=:nextNo 
				AND dd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sloc='8' )
				GROUP BY dd.prodId
				";
			    $stmt = $pdo->prepare($sql);
			    $stmt->bindParam(':nextNo', $nextNo);
			    $stmt->execute();
				
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'doNo' => $nextNo));	
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

