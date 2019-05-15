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

				if($deliveryDate < date("Y-m-d")){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Delivery Date incorrect.'));
					exit();
				}
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT ppNo FROM prepare WHERE ppNo=:ppNo AND statusCode='P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':ppNo', $ppNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Prepare no. incorrect.'));
					exit();
				}	

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
				GROUP BY itm.prodCodeId
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
				echo json_encode(array('success' => true, 'message' => 'Data Confirmed', 'doNo' => $doNo));	
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
				echo json_encode(array('success' => true, 'message' => 'Data rejected', 'doNo' => $doNo));	
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
				case 'admin' : case 'whSup' : 
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
				//$isClose = $_POST['isClose'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT do.doNo, pp.pickNo, do.deliveryDate, do.statusCode FROM delivery_header do
				INNER JOIN prepare pp ON pp.ppNo=do.ppNo 
				WHERE do.doNo=:doNo AND do.statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					
					$hdr=$stmt->fetch();
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'.$hdr['statusCode']));
					exit();
				}else{
					$sql = "UPDATE delivery_header hdr SET  hdr.statusCode='W' WHERE hdr.doNo=:doNo ";
					$stmt = $pdo->prepare($sql);		
					$stmt->bindParam(':doNo', $doNo);
					$stmt->execute();
				}
				$hdr=$stmt->fetch();
				$pickNo=$hdr['pickNo'];
				
				$hdrIssueTime = new DateTime($hdr['deliveryDate']); 
				$hdrIssueDate = $hdrIssueTime->format('Y-m-d');	

				//Query 1: Check Prev Cloosing Date	
				$sql = "SELECT `closingDate` FROM `stk_closing` WHERE statusCode='A' AND closingDate >= :closingDate LIMIT 1 ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':closingDate', $hdrIssueDate);
				$stmt->execute();
				if($stmt->rowCount() == 1 ){
					//return JSON 
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Incorrect Bill Date.'));
					exit();
				}
				//End Closing Date




				$sql = "SELECT ct.locationCode FROM sale_header sh
				INNER JOIN customer ct ON ct.id=sh.custId 
				WHERE sh.soNo=:soNo LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':soNo', $soNo);
				$stmt->execute();
				$sloc=$stmt->fetch()['locationCode'];
				if ( $sloc == "" ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Customer Location Error.'));
					exit();
				}
				if($sloc=='L') { $sloc='8'; } 
				
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
				
				
			    //Query 2: Delete from Shelf
				$sql = "DELETE smi
				FROM `wh_shelf_map_item` smi
				INNER JOIN receive_detail rDtl ON rDtl.id=smi.recvProdId 
				INNER JOIN delivery_detail dDtl ON dDtl.prodItemId=rDtl.prodItemId AND dDtl.doNo=:doNo ";
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
				
				$isAutoClose=false;
				//Cloase SO if all product order qty <= sent qty (all DO) 
				$sql = "SELECT COUNT(*) as countTotal FROM (
				    SELECT sd.prodId
				    , sum(sd.qty) as sumOrderQty 
				    ,IFNULL((SELECT sum(dd.qty) FROM delivery_header dh 
				            LEFT JOIN delivery_prod dd ON dd.doNo=dh.doNo 
				            WHERE dh.soNo=sh.soNo 
				            AND dd.prodId=sd.prodId 
				            GROUP BY dd.prodId),0) as sumSentQty 
				    FROM sale_header sh 
				    INNER JOIN sale_detail sd ON sd.soNo=sh.soNo 
				    WHERE sh.soNo=:soNo  
				    GROUP BY sd.prodId
				    ) AS tmp
				WHERE tmp.sumOrderQty>tmp.sumSentQty";
				$stmt = $pdo->prepare($sql);		
				$stmt->bindParam(':soNo', $soNo);
				$stmt->execute();				
				if( $stmt->fetch()['countTotal'] == 0 ){
					$sql = "UPDATE sale_header SET isClose='Y' WHERE soNo=:soNo ";
					$stmt = $pdo->prepare($sql);		
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();

					$isAutoClose=true;
				}
				/*
				$sql = "
					SELECT sd.prodId
					, sum(sd.qty) as sumOrderQty 
					,IFNULL((SELECT sum(dd.qty) FROM delivery_header dh 
							LEFT JOIN delivery_prod dd ON dd.doNo=dh.doNo 
							WHERE dh.soNo=sh.soNo 
							AND dd.prodId=sd.prodId 
							GROUP BY dd.prodId),0) as sumSentQty 
					FROM sale_header sh 
					INNER JOIN sale_detail sd ON sd.soNo=sh.soNo 
					WHERE sh.soNo=:soNo  
					GROUP BY sd.prodId
					";
					$stmt = $pdo->prepare($sql);		
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();

					$diffCount=0; while($row = $stmt->fetch() ){
						if ( $row['sumOrderQty'] > $row['sumSentQty'] ) {
							$diffCount+=1;
						}
					}
						
					if( $diffCount == 0 ){
						$sql = "UPDATE sale_header SET isClose='Y' WHERE soNo=:soNo ";
						$stmt = $pdo->prepare($sql);		
						$stmt->bindParam(':soNo', $soNo);
						$stmt->execute();

						$isAutoClose=true;
					}
				*/

				//Query 5: UPDATE STK BAl
				/*$sql = "UPDATE stk_bal sb
				INNER JOIN delivery_prod dp ON dp.prodId=sb.prodId AND dp.doNo=:nextNo 
				SET sb.delivery = sb.delivery + dp.qty
				, sb.sales =  sb.sales - dp.qty 
				, sb.balance = sb.balance - dp.qty 
				WHERE sb.sloc=:sloc 
				";
			    $stmt = $pdo->prepare($sql);
			    $stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':sloc', $sloc);
			    $stmt->execute();
				
				//Query 6: INSERT STK BAl
				$sql = "INSERT INTO stk_bal (prodId, sloc, delivery, sales, balance) 
				SELECT dd.prodId, :sloc, dd.qty, -1*dd.qty, -1*dd.qty  
				FROM delivery_prod dd 
				WHERE dd.doNo=:nextNo 
				AND dd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sloc=:sloc2 )
				";
			    $stmt = $pdo->prepare($sql);
			    $stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':sloc', $sloc);
				$stmt->bindParam(':sloc2', $sloc);
			    $stmt->execute();
				*/
				
				//We've got this far without an exception, so commit the changes.
			    $pdo->commit();
				
			    //return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'doNo' => $nextNo, 'isAutoClose' => $isAutoClose ));	
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
			//Check user roll.
			switch($s_userGroupCode){
				case 'admin' : case 'whSup' : case 'pdSup' : case 'whMgr' : case 'pdMgr' : 
					break;
				default : 
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
					exit();
			}

			$doNo = $_POST['doNo'];

			//We will need to wrap our queries inside a TRY / CATCH block.
			//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
			try{
				//Check is uniqe item for re-active to A
				$sql = "SELECT prodItemId, COUNT(id)  FROM receive_detail WHERE prodItemId IN (SELECT prodItemId FROM `delivery_detail` WHERE doNo=:doNo)
				GROUP BY prodItemId HAVING COUNT(id) > 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 0 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Some Item Duplicate Receiving. Contact Admin.'));
					exit();
				}

				//We start our transaction.
				$pdo->beginTransaction();
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT * FROM delivery_header WHERE doNo=:doNo AND statusCode='P' LIMIT 1";
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
				$hdr = $stmt->fetch();

				// if(trim($hdr['rcNo'])<>"" ){
				// 	//return JSON
				// 	header('Content-Type: application/json');
				// 	echo json_encode(array('success' => false, 'message' => 'Sending has been received.'));
				// 	exit();
				// }			
				// $fromCode = $hdr['fromCode'];
				// $toCode = $hdr['toCode'];
							
				switch($s_userGroupCode){
					case 'admin' : 
						break;
					default : 
						//Query 1: Check Prev Cloosing Date
						$hdrIssueTime = strtotime($hdr['deliveryDate']);
						$hdrIssueDate = date("Y-m-d", $hdrIssueTime);		
						$sql = "SELECT `closingDate` FROM `stk_closing` WHERE statusCode='A' AND closingDate >= :closingDate LIMIT 1 ";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':closingDate', $hdrIssueDate);
						$stmt->execute();
						if($stmt->rowCount() == 1 ){
							//return JSON 
							header('Content-Type: application/json');
							echo json_encode(array('success' => false, 'message' => 'Incorrect Bill Date.'));
							exit();
						}
						//End Closing Date
				}				

				//Remove Delivery
				$sql = "UPDATE `delivery_header`
				SET `statusCode`='X'
				, `updateTime`=NOW()
				, `updateById`=:userId 
				WHERE `doNo` = :doNo 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':userId', $s_userId);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();

				//Remove Prepare
				$sql = "UPDATE `prepare`
				SET `statusCode`='X'
				, `updateTime`=NOW()
				, `updateById`=:userId 
				WHERE `ppNo` = (SELECT `ppNo`
						FROM `delivery_header` WHERE `doNo`=:doNo)
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':userId', $s_userId);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();

				//Remove Picking
				$sql = "UPDATE `picking`
				SET `statusCode`='X'
				, `updateTime`=NOW()
				, `updateById`=:userId 
				WHERE `pickNo` = (SELECT `pickNo` 
					FROM `prepare` WHERE `ppNo` = (SELECT `ppNo`
						FROM `delivery_header` WHERE `doNo`=:doNo)
					)
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':userId', $s_userId);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();

				//Update Receive Items
				$sql = "UPDATE receive_detail 
				SET statusCode='A' 
				WHERE prodItemId IN (SELECT prodItemId FROM `delivery_detail` WHERE doNo=:doNo)
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':doNo', $doNo);
				$stmt->execute();

				//Re-Open if Closed Sales Order 
				$sql = "UPDATE sale_header
				SET isClose='N' 
				WHERE soNo = :soNo 
				AND isClose = 'Y' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':soNo', $hdr['soNo']);
				$stmt->execute();


				
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Removed', 'doNo' => $doNo));	
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

