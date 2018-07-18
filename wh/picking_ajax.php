<?php

include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$tb='picking';

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
				$soNo = $_POST['soNo'];	
				$pickNo = 'PI-'.substr(str_shuffle(MD5(microtime())), 0, 7);
				$pickDate = $_POST['pickDate'];
				$remark = $_POST['remark'];
				
				$pickDate = str_replace('/', '-', $pickDate);
				$pickDate = date("Y-m-d",strtotime($pickDate));
					
				$sql = "INSERT INTO `picking`
				(`pickNo`, `soNo`, `pickDate`, `isFinish`, `remark`, `statusCode`, `createTime`, `createById`) 
				VALUES (:pickNo,:soNo,:pickDate,'N', :remark,'B',now(),:s_userId)
				";
						
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->bindParam(':pickDate', $pickDate);
				$stmt->bindParam(':remark', $remark);
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->bindParam(':soNo', $soNo);
				$stmt->execute();
						
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'pickNo'=> $pickNo));
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
		case 'item_add' :
			try{	
				$pickNo = $_POST['pickNo'];
				$prodId = $_POST['prodId'];
				$saleItemId = $_POST['saleItemId'];
				
				$sql = "SELECT qty FROM sale_detail WHERE id=:id ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $saleItemId);	
				$stmt->execute();
				$row=$stmt->fetch();
				$orderQty=$row['qty'];

				$sql = "SELECT sum(qty) as sumBookedQty FROM picking_detail WHERE pickNo<>:pickNo AND saleItemId=:saleItemId ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->bindParam(':saleItemId', $saleItemId);	
				$stmt->execute();
				$row=$stmt->fetch();
				$sumBookedQty=$row['sumBookedQty'];

				$sumPickQty=0;
				foreach($_POST['pickQty'] as $index => $item )
				{	
					if($item<>0){
						$issueDate=$_POST['issueDate'][$index];
						$grade=$_POST['grade'][$index];
						$meter=$_POST['meter'][$index];
						$pickQty=$_POST['pickQty'][$index];
						$balanceQty=$_POST['balanceQty'][$index];
						$gradeName="";
						switch($grade){
							case '0' : $gradeName='A'; break;
							case '1' : $gradeName='B'; break;
							case '2' : $gradeName='N'; break;
							default : $gradeName='N/A'; 
						}

						if($pickQty>$balanceQty){
							header('Content-Type: application/json');
							$errors="Pick : issue date = ".$issueDate.", grade = ".$gradeName.", meter = ".$meter." is OVER STOCK.";
							echo json_encode(array('success' => false, 'message' => $errors));
							exit();
						}
						if( ($pickQty % $meter) > 0 ){
							header('Content-Type: application/json');
							$errors="Pick quantity [ ".number_format($pickQty,0,'.',',')." ] with Meter [ ".number_format($meter,0,'.',',')." ] is incorrect.";
							echo json_encode(array('success' => false, 'message' => $errors));
							exit();
						}
						$sumPickQty+=$_POST['pickQty'][$index];
					}											
				}
				if( ($sumPickQty+$sumBookedQty) > $orderQty ){
					header('Content-Type: application/json');
					$errors="Pick quantity total [ ".number_format($sumPickQty,0,'.',',')." ] is over then order quantity / remain quantity [ ".number_format($orderQty,0,'.',',')."/".number_format( ($orderQty-($sumBookedQty)) ,0,'.',',')." ] ";
					echo json_encode(array('success' => false, 'message' => $errors));
					exit();
				}


				$pdo->beginTransaction();	
								
				if(!empty($_POST['pickQty']) and isset($_POST['pickQty']))
				{
					$sql = "DELETE FROM `picking_detail` WHERE pickNo=:pickNo AND saleItemId=:saleItemId ";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':pickNo', $pickNo);	
					$stmt->bindParam(':saleItemId', $saleItemId);	
					$stmt->execute();
					
					//$arrProdItems=explode(',', $prodItems);
					foreach($_POST['pickQty'] as $index => $item )
					{	
						if($item<>0){
							if($_POST['pickQty'][$index]>$_POST['balanceQty'][$index]){
								header('Content-Type: application/json');
								$errors = "Some Product is over stock.";
								echo json_encode(array('success' => false, 'message' => $errors));
								exit();
							}
							$sql = "INSERT INTO `picking_detail` 
							(`pickNo`,`saleItemId`, `prodId`, `issueDate`, `grade`, `meter`, `qty`) 
							VALUES
							(:pickNo,:saleItemId, :prodId,:issueDate,:grade,:meter,:pickQty)";
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':pickNo', $pickNo);	
							$stmt->bindParam(':saleItemId', $saleItemId);	
							$stmt->bindParam(':prodId', $prodId);	
							$stmt->bindParam(':issueDate', $_POST['issueDate'][$index]);	
							$stmt->bindParam(':grade', $_POST['grade'][$index]);	
							$stmt->bindParam(':meter', $_POST['meter'][$index]);	
							$stmt->bindParam(':pickQty', $_POST['pickQty'][$index]);	
							$stmt->execute();
						}											
					}
				}
				$pdo->commit();
				
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.', 'pickNo' => $pickNo));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//Rollback the transaction.
				$pdo->rollBack();
				//return JSON
				header('Content-Type: application/json');
				$errors = "Error on Data Update. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			break;
		case 'add_item_delete' :
			try{
		
				$id = $_POST['id'];
				
				$sql = "DELETE FROM picking_detail WHERE id=:id 
						";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $id);
				$stmt->execute();
				
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Delete Complete.'));
			} 
			//Our catch block will handle any exceptions that are thrown.
			catch(Exception $e){
				//return JSON
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
				$pickNo = $_POST['pickNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT pickNo FROM picking WHERE pickNo=:pickNo AND statusCode<>'P' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: DELETE Detail
				$sql = "DELETE FROM `picking_detail` WHERE pickNo=:pickNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);	
				$stmt->execute();
				
				//Query 2: DELETE Header
				$sql = "DELETE FROM `picking` WHERE pickNo=:pickNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);	
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
				$pickNo = $_POST['pickNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT pickNo FROM picking WHERE pickNo=:pickNo AND statusCode='B' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: UPDATE DATA
				$sql = "UPDATE `picking` SET statusCode='C'
						, confirmTime=now()
						, confirmById=:s_userId 
						WHERE pickNo=:pickNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);		
				$stmt->bindParam(':s_userId', $s_userId);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'pickNo' => $pickNo));	
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
				$pickNo = $_POST['pickNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT pickNo FROM picking WHERE pickNo=:pickNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}	
					
				//Query 1: UPDATE DATA
				$sql = "UPDATE `picking` SET statusCode='B'
						WHERE pickNo=:pickNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);	
				$stmt->execute();
					
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'pickNo' => $pickNo));	
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
				$pickNo = $_POST['pickNo'];	
				
				//We start our transaction.
				$pdo->beginTransaction();
				
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT pickNo FROM picking WHERE pickNo=:pickNo AND statusCode='C' LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){		
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}
				
				//Query 1: GET Next Doc No.
				$year = date('Y'); $name = 'picking'; $prefix = 'Pi'.date('y'); $cur_no=1;
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
				$sql = "UPDATE `picking` SET statusCode='P'
						, pickNo=:nextNo
						, approveTime=now()
						, approveById=:s_userId 
						WHERE pickNo=:pickNo";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':s_userId', $s_userId);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
					
				//Query 2: UPDATE DATA
				$sql = "UPDATE picking_detail SET pickNo=:nextNo WHERE pickNo=:pickNo ";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':nextNo', $nextNo);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
				
				//UPDATE doc running.
				$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
				$stmt = $pdo->prepare($sql);		
				$stmt->execute(array($cur_no, $year, $name));
					
				
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data approved', 'pickNo' => $nextNo));	
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
				case 'admin' : case 'whSup' :
					break;
				default : 
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
					exit();
			}

			$pickNo = $_POST['pickNo'];

			//We will need to wrap our queries inside a TRY / CATCH block.
			//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
			try{
				//We start our transaction.
				$pdo->beginTransaction();
				//Query 1: Check Status for not gen running No.
				$sql = "SELECT p.*, pp.ppNo 
				FROM picking p 
				LEFT JOIN prepare pp ON pp.pickNo=p.pickNo 
				WHERE p.pickNo=:pickNo 
				AND p.statusCode='P' 
				LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
				$row_count = $stmt->rowCount();	
				if($row_count != 1 ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
					exit();
				}				
				$hdr = $stmt->fetch();
				if(trim($hdr['ppNo'])<>"" ){
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => false, 'message' => 'Picking has been prepared.'));
					exit();
				}										
				
				//Query 1: UPDATE DATA
				$sql = "UPDATE picking SET statusCode='X'
				, updateTime=now()
				, updateById=:updateById
				WHERE pickNo=:pickNo  
				AND statusCode='P' 
				";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':updateById', $s_userId);
				$stmt->bindParam(':pickNo', $pickNo);
				$stmt->execute();
								
				//We've got this far without an exception, so commit the changes.
				$pdo->commit();
				
				//return JSON
				header('Content-Type: application/json');
				echo json_encode(array('success' => true, 'message' => 'Data Approved', 'sdNo' => $pickNo));	
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

