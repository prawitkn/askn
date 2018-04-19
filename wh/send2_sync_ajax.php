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
		case 'sync' :				
			try{
				$isSync = $_POST['isSync'];
				$sendDate = $_POST['sendDate'];
				$fromCode = $_POST['fromCode'];
				$toCode = $_POST['toCode'];
				$prodId = $_POST['prodId'];
				
				$sendDate = str_replace('/', '-', $sendDate);
				$sendDate = date("Y-m-d",strtotime($sendDate));

				//if( $isSync == 1 AND isset($_POST['sendDate'])){
					include_once '../db/db_sqlsrv.php';
					
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
					
				//}	//if is_sync
				




				
				$sql = "SELECT hdr.sendId, dtl.`productItemId`, itm.`barcode`, itm.`issueDate`
				, itm.`machineId`, itm.`seqNo`, itm.`NW`, itm.`GW`, itm.`qty`, itm.`packQty`, itm.`grade`, itm.`gradeDate`
				, itm.`refItemId`, itm.`itemStatus`, itm.`remark`, itm.`problemId`					
				, itm.prodCodeId as prodId, prd.code as prodCode
				, IFNULL((SELECT sHdr.sdNo FROM send sHdr
											INNER JOIN send_detail sDtl ON sDtl.sdNo=sHdr.sdNo
											WHERE sHdr.statusCode IN ('C','P') AND sDtl.prodItemId=itm.prodItemId LIMIT 1),'') as sentNo 
				,CASE itm.grade
					WHEN 0 THEN 'A' 
					WHEN 1 THEN 'B' 	
					WHEN 2 THEN 'N' 	
					ELSE 'N/A'
				END AS gradeName 
				FROM send_mssql hdr  
				INNER JOIN send_detail_mssql dtl ON dtl.sendId=hdr.sendId 
				INNER JOIN product_item itm ON itm.prodItemId=dtl.productItemId 
				LEFT JOIN product prd ON prd.id=itm.prodCodeId
				WHERE 1=1 ";
				if($sendDate<>"") $sql.="AND hdr.issueDate=:sendDate ";
				if($fromCode<>"") $sql.="AND hdr.fromCode=:fromCode ";
				if($toCode<>"") $sql.="AND hdr.toCode=:toCode ";
				if($prodId<>"") $sql.="AND itm.prodCodeId=:prodId ";
				$sql.="ORDER BY hdr.sendId  LIMIT 200  "; 
				
				$stmt = $pdo->prepare($sql);
				if($sendDate<>"") $stmt->bindParam(':sendDate', $sendDate );
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
		default : 
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
	}//end switch action
}
//end if else check action.
?>     

