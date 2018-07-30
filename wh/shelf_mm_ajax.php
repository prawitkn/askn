<?php

include 'session.php'; /*$s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDept = $row_user['userDept'];*/
$tb='';

if(!isset($_POST['action'])){		
	header('Content-Type: application/json');
	echo json_encode(array('success' => false, 'message' => 'No action.'));
}else{
	switch($_POST['action']){
		case 'searchItem' :				
			try{
				$shelfId = $_POST['shelfId'];
				$issueDate = $_POST['issueDate'];
				$prodId = $_POST['prodId'];
								
				if($issueDate<>""){
					$issueDate = str_replace('/', '-', $issueDate);
					$issueDate = date("Y-m-d",strtotime($issueDate));
				}

				$sql = "SELECT rd.id as recvProdId, rd.prodItemId, rd.rcNo 
				, itm.prodCodeId as prodId, itm.barcode, itm.NW, itm.GW, itm.grade, itm.qty, itm.issueDate
				, prd.code as prodCode 
				FROM wh_shelf_map_item smi 
				INNER JOIN receive_detail rd ON rd.id=smi.recvProdId
				INNER JOIN product_item itm ON itm.prodItemId=rd.prodItemId
				LEFT JOIN product prd ON prd.id=itm.prodCodeId 
				WHERE 1=1 
				AND smi.shelfId=:shelfId ";
				
				//if($shelfId<>"") $sql.="AND hdr.issueDate=:sendDate ";

				$sql.="ORDER BY itm.prodCodeId, itm.barcode "; 
				
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':shelfId', $shelfId );
				
			
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
		case 'searchItemLot' :				
			try{
				$shelfId = $_POST['shelfId'];
				

				$sql = "SELECT DISTINCT itm.prodCodeId as prodId, itm.issueDate, itm.grade, itm.qty 
				, prd.code as prodCode 
				FROM wh_shelf_map_item smi 
				INNER JOIN receive_detail rd ON rd.id=smi.recvProdId
				INNER JOIN product_item itm ON itm.prodItemId=rd.prodItemId
				LEFT JOIN product prd ON prd.id=itm.prodCodeId 
				WHERE 1=1 
				AND smi.shelfId=:shelfId ";				

				$sql.="ORDER BY prd.code "; 
				
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':shelfId', $shelfId );
				
			
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
		case 'item_move' :		
			try{	
				$shelfIdFrom = $_POST['shelfIdFrom'];
				$shelfIdTo = $_POST['shelfIdTo'];
				$remark = $_POST['remark'];
				
				$pdo->beginTransaction();	
				
				$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS wh_shelf_map_item_tmp AS (SELECT `shelfId`, `recvProdId`FROM wh_shelf_map_item WHERE 1<>1 )
					";				
					$stmt = $pdo->prepare($sql);	
					$stmt->execute();	

				if(!empty($_POST['itmId']) and isset($_POST['itmId']))
				{					
					//$arrProdItems=explode(',', $prodItems);
					foreach($_POST['itmId'] as $index => $item )
					{	
						$sql = "INSERT INTO `wh_shelf_map_item_tmp`
						(`shelfId`, `recvProdId`) 
						VALUES 
						(:shelfId, :recvProdId)
						";
						$arrItm=explode(',', $item);
						$stmt = $pdo->prepare($sql);			
						//$stmt->bindParam(':sendId', $arrItm[0]);		
						$stmt->bindParam(':shelfId', $shelfIdTo);	
						$stmt->bindParam(':recvProdId', $arrItm[1]);	
						$stmt->execute();			
					}

					$sql = "INSERT INTO `shelf_movement`
					(`shelfIdFrom`, `shelfIdTo`, `remark`, `statusCode`, `createById`) 
					VALUES 
					(:shelfIdFrom, :shelfIdTo, :remark, 'C', :createById)
					";
					$stmt = $pdo->prepare($sql);			
					//$stmt->bindParam(':sendId', $arrItm[0]);	
					$stmt->bindParam(':shelfIdFrom', $shelfIdFrom);		
					$stmt->bindParam(':shelfIdTo', $shelfIdTo);	
					$stmt->bindParam(':remark', $remark);	
					$stmt->bindParam(':createById', $s_userId);	
					$stmt->execute();	
					$hdrId=$pdo->lastInsertId();

					$sql = "INSERT INTO `shelf_movement_detail`
					(`hdrId`, `recvProdId`) 
					SELECT :hdrId, tmp.recvProdId 
					FROM wh_shelf_map_item_tmp tmp
					";
					$stmt = $pdo->prepare($sql);			
					$stmt->bindParam(':hdrId', $hdrId);	
					$stmt->execute();	

					$sql = "DELETE prd 
					FROM `wh_shelf_map_item` prd  
					INNER JOIN wh_shelf_map_item_tmp tmp ON prd.recvProdId=tmp.recvProdId 
					";			
					$stmt = $pdo->prepare($sql);		
					$stmt->execute();	

					$sql = "INSERT INTO `wh_shelf_map_item` 
					SELECT `shelfId`, `recvProdId`, 'A' FROM wh_shelf_map_item_tmp
					";			
					$stmt = $pdo->prepare($sql);		
					$stmt->execute();	

				}
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
				$errors = "Error on Data Update. Please try again. " . $e->getMessage();
				echo json_encode(array('success' => false, 'message' => $errors));
			}
			
			break;
		case 'item_move_lot' :		
			try{	
				$shelfIdFrom = $_POST['shelfIdFrom'];
				$shelfIdTo = $_POST['shelfIdTo'];
				$remark = $_POST['remark'];
				
				$pdo->beginTransaction();	
				
				$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS wh_shelf_map_item_tmp AS (SELECT `shelfId`, `recvProdId`FROM wh_shelf_map_item WHERE 1<>1 )
					";				
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();	
					
				$sql = "
				CREATE TEMPORARY TABLE IF NOT EXISTS temp(
					prodId INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
					grade INT,
					qty INT,
					issueDate DATE) ENGINE = MEMORY;
				";				
				$stmt = $pdo->prepare($sql);	
				$stmt->execute();	

				if(!empty($_POST['itmId']) and isset($_POST['itmId']))
				{					
					//$arrProdItems=explode(',', $prodItems);
					foreach($_POST['itmId'] as $index => $item )
					{	
						$sql = "INSERT INTO `temp`
						(`prodId`, `grade`, `qty`, `issueDate`) 
						VALUES 
						(:prodId, :grade, :qty, :issueDate)
						";
						$arrItm=explode(',', $item);
						$stmt = $pdo->prepare($sql);				
						$stmt->bindParam(':prodId', $arrItm[0]);	
						$stmt->bindParam(':grade', $arrItm[1]);	
						$stmt->bindParam(':qty', $arrItm[2]);	
						$stmt->bindParam(':issueDate', $arrItm[3]);	
						$stmt->execute();			
					}
					
					$sql = "INSERT INTO `wh_shelf_map_item_tmp` 
					SELECT :shelfIdTo, rDtl.id 
					FROM temp tmp
					INNER JOIN product_item itm ON itm.prodCodeId=tmp.prodId AND itm.grade=tmp.grade 
										AND itm.qty=tmp.qty AND itm.issueDate=tmp.issueDate
					INNER JOIN receive_detail rDtl ON rDtl.prodItemId=itm.prodItemId 
					INNER JOIN wh_shelf_map_item smi ON smi.recvProdId=rDtl.id AND smi.shelfId=:shelfIdFrom 
					";			
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':shelfIdTo', $shelfIdTo);	
					$stmt->bindParam(':shelfIdFrom', $shelfIdFrom);	
					$stmt->execute();	
										
					$sql = "INSERT INTO `shelf_movement`
					(`shelfIdFrom`, `shelfIdTo`, `remark`, `statusCode`, `createById`) 
					VALUES 
					(:shelfIdFrom, :shelfIdTo, :remark, 'C', :createById)
					";
					$stmt = $pdo->prepare($sql);			
					//$stmt->bindParam(':sendId', $arrItm[0]);	
					$stmt->bindParam(':shelfIdFrom', $shelfIdFrom);		
					$stmt->bindParam(':shelfIdTo', $shelfIdTo);	
					$stmt->bindParam(':remark', $remark);	
					$stmt->bindParam(':createById', $s_userId);	
					$stmt->execute();	
					$hdrId=$pdo->lastInsertId();

					$sql = "INSERT INTO `shelf_movement_detail`
					(`hdrId`, `recvProdId`) 
					SELECT :hdrId, tmp.recvProdId 
					FROM wh_shelf_map_item_tmp tmp
					";
					$stmt = $pdo->prepare($sql);			
					$stmt->bindParam(':hdrId', $hdrId);	
					$stmt->execute();	

					$sql = "DELETE prd 
					FROM `wh_shelf_map_item` prd  
					INNER JOIN wh_shelf_map_item_tmp tmp ON prd.recvProdId=tmp.recvProdId 
					";			
					$stmt = $pdo->prepare($sql);		
					$stmt->execute();	

					$sql = "INSERT INTO `wh_shelf_map_item` 
					SELECT `shelfId`, `recvProdId`, 'A' FROM wh_shelf_map_item_tmp
					";			
					$stmt = $pdo->prepare($sql);		
					$stmt->execute();		

				}
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
				$errors = "Error on Data Update. Please try again. " . $e->getMessage();
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

