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
		case 'searchItem' :				
			try{
				$isSync = $_POST['isSync'];
				$sdNo = $_POST['sdNo'];
				$sendDate = $_POST['sendDate'];
				$issueDate = $_POST['issueDate'];
				$fromCode = $_POST['fromCode'];
				$toCode = $_POST['toCode'];
				$prodId = $_POST['prodId'];
				
				
				if($sendDate<>""){
					$sendDate = str_replace('/', '-', $sendDate);
					$sendDate = date("Y-m-d",strtotime($sendDate));
				}
				if($issueDate<>""){
					$issueDate = str_replace('/', '-', $issueDate);
					$issueDate = date("Y-m-d",strtotime($issueDate));
				}
				if( $isSync == 1 AND isset($_POST['sendDate'])){
					include_once '../db/db.php';
					
					//TRUNCATE temp 
					$sql = "TRUNCATE TABLE send_mssql_tmp";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
					
					$sql = "TRUNCATE TABLE send_detail_mssql_tmp";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
						
					$sql = "TRUNCATE TABLE product_item_temp";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();

					$sql = "SELECT DISTINCT  hdr.[SendID], hdr.[SendNo], CONVERT(VARCHAR, hdr.[IssueDate], 121) as IssueDate
					  , left(itm.[ItemCode],1) as fromCode 
					  , [CustomerID]
					  FROM [send] hdr, [askn].[dbo].[send_detail] dtl, [product_item] itm
					  WHERE hdr.SendID=dtl.SendID 
					  AND dtl.[ProductItemID]=itm.[ProductItemID]
					  AND hdr.[isCustomer]='N' 
					  AND hdr.[IssueDate] = '$sendDate'
					  ";
					  switch($s_userGroupCode){ 
						case 'whOff' :  case 'whSup' : 
								$sql .= "AND left(itm.[ItemCode],1) IN (0,7,8) ";
							break;
						case 'pdOff' :  case 'pdSup' :
								$sql .= "AND left(itm.[ItemCode],1) = '".$s_userDeptCode."' ";
							break;
						default : //case 'it' : case 'admin' : 
					  }
					//echo $sql;
					$msResult = sqlsrv_query($ssConn, $sql);
					$msRowCount = 0;
					$c = 1;
					set_time_limit(0);
					if($msResult){
						while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {	
							//Insert Header mysql from mssql
							$sql = "INSERT INTO  `send_mssql_tmp` 
							(`sendId`, `issueDate`, `customerId`, `fromCode`) 
							VALUES
							(:SendID,:IssueDate,:CustomerID,:fromCode)
							";		
							
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':SendID', $msRow['SendID']);	
							$stmt->bindParam(':IssueDate', $msRow['IssueDate']);
							$stmt->bindParam(':CustomerID', $msRow['CustomerID']);	
							$stmt->bindParam(':fromCode', $msRow['fromCode']);			
							$stmt->execute();

							$msRowCount+=1;
						}
						//end while mssql
					}else{
						echo sqlsrv_error();
					}
					//if
					
					sqlsrv_free_stmt($msResult);
									
					
					$sql = "  SELECT itm.[ProductItemID]
					  ,itm.[ProductID]
					  ,itm.[ItemCode]
					  , CONVERT(VARCHAR, itm.[IssueDate], 121) as IssueDate
					  ,itm.[MachineID]
					  ,itm.[SeqNo]
					  ,itm.[NW]
					  ,itm.[GW]
					  ,itm.[Length]
					  ,itm.[Grade]
					  , CONVERT(VARCHAR, itm.[IssueGrade], 121) as IssueGrade
					  ,itm.[UserID]
					  ,itm.[RefItemID]
					  ,itm.[ItemStatus]
					  ,itm.[Remark]
					  ,itm.[RecordDate]
					  ,itm.[ProblemID]
					  ,dtl.[SendID], dtl.[Remark] 
				  FROM [send_detail] dtl, [product_item] itm 
				  WHERE dtl.[ProductItemID]=itm.[ProductItemID]
				  AND dtl.[SendID] IN (  SELECT DISTINCT  hdr.[SendID] 
									  FROM [send] hdr, [send_detail] dtl, [product_item] itm
									  WHERE hdr.SendID=dtl.SendID 
									  AND dtl.[ProductItemID]=itm.[ProductItemID]
									  AND hdr.[IssueDate] = '$sendDate' )
					  ";
				  switch($s_userGroupCode){ 
					case 'whOff' :  case 'whSup' : 
							//$sql .= "AND left(itm.[ItemCode],1) IN ('0','7','8','9') ";
						break;
					case 'pdOff' :  case 'pdSup' :
							$sql .= "AND left(itm.[ItemCode],1) = '".$s_userDeptCode."' ";
						break;
					default : //case 'it' : case 'admin' : 
				  }
					//echo $sql;
					$msResult = sqlsrv_query($ssConn, $sql);
					$msRowCount = 0;
					$c = 1;
					set_time_limit(0);
					if($msResult){
					while ($msRow = sqlsrv_fetch_array($msResult, SQLSRV_FETCH_ASSOC))  {	
						//Insert mysql from mssql
						$sql = "INSERT INTO  `product_item_temp` 
						(`prodItemId`, `prodId`, `barcode`, `issueDate`, `machineId`, `seqNo`, `NW`, `GW`
						, `qty`, `packQty`, `grade`, `gradeDate`, `refItemId`, `itemStatus`, `remark`, `problemId`) 
						VALUES
						(:ProductItemID,:ProductID,:ItemCode,:IssueDate,:MachineID,:SeqNo,:NW,:GW
						,:Length,null,:Grade,:IssueGrade,:RefItemID,:ItemStatus,:Remark,:ProblemID
						)
						";		
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':ProductItemID', $msRow['ProductItemID']);	
						$stmt->bindParam(':ProductID', $msRow['ProductID']);	
						$stmt->bindParam(':ItemCode', $msRow['ItemCode']);	
						$stmt->bindParam(':IssueDate', $msRow['IssueDate']);	
						$stmt->bindParam(':MachineID', $msRow['MachineID']);	
						$stmt->bindParam(':SeqNo', $msRow['SeqNo']);	
						$stmt->bindParam(':NW', $msRow['NW']);			
						$stmt->bindParam(':GW', $msRow['GW']);	
						
						$stmt->bindParam(':Length', $msRow['Length']);	
						$stmt->bindParam(':Grade', $msRow['Grade']);	
						$stmt->bindParam(':IssueGrade', $msRow['IssueGrade']);	
						$stmt->bindParam(':RefItemID', $msRow['RefItemID']);	
						$stmt->bindParam(':ItemStatus', $msRow['ItemStatus']);	
						$stmt->bindParam(':Remark', $msRow['Remark']);	
						$stmt->bindParam(':ProblemID', $msRow['ProblemID']);		
						
						$stmt->execute();
						
						$sql = "INSERT INTO  `send_detail_mssql_tmp` 
						(`productItemId`, `sendId`, `remark`) 
						VALUES
						(:ProductItemID, :SendID, :Remark)
						";		
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':ProductItemID', $msRow['ProductItemID']);	
						$stmt->bindParam(':SendID', $msRow['SendID']);	
						$stmt->bindParam(':Remark', $msRow['Remark']);	
						$stmt->execute();

						$msRowCount+=1;
					}
					//end while mssql
					}else{
						echo sqlsrv_error();
					}
					//if
					
					sqlsrv_free_stmt($msResult);
					//END MSSQL 
											
					//PRODUCT ITEM (INSERT ONLY) 
					//Update prodCodeId in product item.////////////////////////////////////////////
					$sql = "UPDATE product_item_temp tmp 
					INNER JOIN product_mapping map ON map.invProdId=tmp.prodId 
					SET tmp.prodCodeId=map.wmsProdId 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();	
					//Update prodCodeId in product item.////////////////////////////////////////////
											
					//Delete production only not approve sending.
					/*$sql = "DELETE FROM product_item 
					WHERE prodItemId IN (SELECT tmp.prodItemId FROM product_item_temp tmp 
											INNER JOIN send_detail dtl ON dtl.prodItemId=tmp.prodItemId 
											INNER JOIN send hdr ON hdr.sdNo=dtl.sdNo AND hdr.statusCode<>'P')	
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();	*/
						
					//Insert prod with temp
					$sql = "INSERT INTO product_item
					SELECT * FROM product_item_temp 
					WHERE prodItemId NOT IN (SELECT prodItemId FROM product_item)	
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();	
											
					
					/*22	COATING(5)
					23	CUTTING(6)
					57	Inspection(7)
					181	Determinate 
					191	Trash
					209	Weaving(4)
					212	Twisting(2)
					213	Warping(3)
					221	C/O=>In
					222	Warehouse
					223	Scrap
					226	Extra stock
					236	Packing
					238	WH(Export)
					239	ERP
					240	160958 TW
					241	160958 WP
					242	160958 WV
					243	160958 CO
					244	160958 CT
					245	160958 In.
					251	R&D 
					252	ล้างสต็อก 2017*/
					//Begin Sync Sending data.
					$sql = "UPDATE send_mssql_tmp prod 
					SET prod.`toCode`= CASE customerId
						WHEN 22 THEN '5'
						WHEN 23 THEN '6'
						WHEN 57 THEN '8'
						WHEN 181 THEN 'U'
						WHEN 191 THEN 'U' 		
						WHEN 209 THEN '4'
						WHEN 212 THEN '2'
						WHEN 213 THEN '3'
						WHEN 221 THEN 'U'
						WHEN 222 THEN '8'
						WHEN 223 THEN 'U'
						WHEN 226 THEN '8'
						WHEN 236 THEN '8'
						WHEN 238 THEN 'E'
						WHEN 239 THEN 'U' 
						WHEN 240 THEN '2'
						WHEN 241 THEN '3'
						WHEN 242 THEN '4'
						WHEN 243 THEN '5'
						WHEN 244 THEN '6'
						WHEN 245 THEN '8'
						WHEN 251 THEN 'U'
						WHEN 252 THEN 'U'
						ELSE 'U' 
					END
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
						
					//Update prod with temp
					//$sql = "UPDATE send prod 
					//INNER JOIN send_production tmp ON tmp.SendNo=prod.refNo AND prod.statusCode<>'P' 
					//SET prod.`issueDate`=tmp.`issueDate`
					//, prod.`qty`=tmp.`qty`
					//, prod.`fromCode`=tmp.`fromCode`
					//, prod.`isCustomer`=tmp.`isCustomer`
					//, prod.`customerID`=tmp.`customerID`
					//";			
					//$stmt = $pdo->prepare($sql);
					//$stmt->execute();
					
					//Insert prod with temp
					/*$sql = "SELECT `sendID`, `sendNo`, `issueDate`, `qty`, `fromCode`, `toCode` 
					FROM send_production 
					WHERE SendID NOT IN (SELECT refNo FROM send) 
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();*/
							
					//Update productoin header.
					/*$sql = "INSERT INTO send_prod 
					(`sdNo`, `refNo`, `sendDate`, `fromCode`, `toCode`, `remark`, `statusCode`, `createTime`, `createById`)
					SELECT tmp.`sendNo`, tmp.`sendID`, tmp.`issueDate`, tmp.`fromCode`, tmp.`toCode`, tmp.`sendNo`, 'B', NOW(), :s_userId
					FROM send_production tmp
					WHERE NOT EXISTS (SELECT * FROM send_prod hdr WHERE hdr.sdNo=tmp.sdNo 
					";	
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':s_userId', $s_userId );	
					$stmt->execute();*/
					
					
					//Insert new productoin header.
					$sql = "INSERT INTO send_mssql
					(`sendId`, `issueDate`, `customerId`, `fromCode`, `toCode`, `createTime`, `createById`)
					SELECT tmp.`sendId`, tmp.`issueDate`, tmp.customerId, tmp.`fromCode`, tmp.`toCode`, NOW(), :s_userId
					FROM send_mssql_tmp tmp
					WHERE NOT EXISTS (SELECT * FROM send_mssql hdr WHERE hdr.sendId=tmp.sendId )
					";	
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':s_userId', $s_userId );	
					$stmt->execute();
											
					
					//Delete temp if Approved.
					/*$sql = "DELETE FROM send_production_detail 
					WHERE prodItemId IN (SELECT dtl.prodItemId FROM send hdr
										INNER JOIN send_detail dtl ON dtl.sdNo=hdr.sdNo 
										WHERE hdr.statusCode='P' )
					";			
					$stmt = $pdo->prepare($sql);
					$stmt->execute();*/
					
					//Insert productoin detail .
					$sql = "INSERT INTO send_detail_mssql 
					(`sendId`, `productItemId`, `remark`)
					SELECT dtl.sendId, dtl.`productItemId`, dtl.`remark`
					FROM send_detail_mssql_tmp dtl
					WHERE NOT EXISTS (SELECT x.* FROM send_detail_mssql x WHERE dtl.productItemId=x.productItemId AND dtl.sendId=x.sendId) 
					";					
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
					
					/*header("Location: ".$rootPage.".php?sendDate=".$_GET['sendDate']);
					
					exit();*/
					
				}				




				
				$sql = "SELECT hdr.sendId, dtl.`productItemId`, itm.`barcode`, itm.`issueDate`
				, itm.`machineId`, itm.`seqNo`, itm.`NW`, itm.`GW`, itm.`qty`, itm.`packQty`, itm.`grade`, itm.`gradeDate`
				, itm.`refItemId`, itm.`itemStatus`, itm.`remark`, itm.`problemId`					
				, itm.prodCodeId as prodId, prd.code as prodCode
				, IFNULL((SELECT sHdr.sdNo FROM send sHdr
											INNER JOIN send_detail sDtl ON sDtl.sdNo=sHdr.sdNo
											WHERE sHdr.statusCode IN ('C','P') AND sDtl.prodItemId=itm.prodItemId LIMIT 1),'') as sentNo
				, IFNULL((SELECT 1 FROM send sHdr
											INNER JOIN send_detail sDtl ON sDtl.sdNo=sHdr.sdNo
											WHERE sHdr.sdNo=:sdNo  AND sDtl.prodItemId=itm.prodItemId LIMIT 1),0) as isSelected 
				,CASE itm.grade
					WHEN 0 THEN 'A' 
					WHEN 1 THEN 'B' 	
					WHEN 2 THEN 'N' 	
					ELSE 'N/A'
				END AS gradeName 
				FROM send_mssql hdr  
				INNER JOIN send_detail_mssql dtl ON dtl.sendId=hdr.sendId 
				LEFT JOIN product_item itm ON itm.prodItemId=dtl.productItemId 
				LEFT JOIN product prd ON prd.id=itm.prodCodeId
				WHERE 1=1 ";
				if($sendDate<>"") $sql.="AND hdr.issueDate=:sendDate ";
				if($issueDate<>"") $sql.="AND itm.issueDate=:issueDate ";
				if($fromCode<>"") $sql.="AND hdr.fromCode=:fromCode ";
				if($toCode<>"") $sql.="AND hdr.toCode=:toCode ";
				if($prodId<>"") $sql.="AND itm.prodCodeId=:prodId ";
				$sql.="ORDER BY hdr.sendId, itm.barcode "; 
				
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':sdNo', $sdNo );
				if($sendDate<>"") $stmt->bindParam(':sendDate', $sendDate );
				if($issueDate<>"") $stmt->bindParam(':issueDate', $issueDate );
				if($fromCode<>"") $stmt->bindParam(':fromCode', $fromCode);
				if($toCode<>"") $stmt->bindParam(':toCode', $toCode);
				if($prodId<>"") $stmt->bindParam(':prodId', $prodId);
				
			
				$stmt->execute();
				

				$rowCount=$stmt->rowCount();

				$jsonData = array();
				while ($array = $stmt->fetch()) {
					$jsonData[] = $array;
				}				
				//header('Content-Type: application/json');				
				echo json_encode(array('success' => true, 'rowCount' => $rowCount, 'data' => json_encode($jsonData)));
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
					$sql = "DELETE FROM `send_detail` WHERE sdNo=:sdNo 
					";			
					$stmt = $pdo->prepare($sql);			
					$stmt->bindParam(':sdNo', $sdNo);	
					$stmt->execute();	
					
					//$arrProdItems=explode(',', $prodItems);
					foreach($_POST['itmId'] as $index => $item )
					{	
						$sql = "INSERT INTO `send_detail`
						(`refNo`, `prodItemId`, `sdNo`) 
						VALUES 
						(:sendId, :productItemId, :sdNo)
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

