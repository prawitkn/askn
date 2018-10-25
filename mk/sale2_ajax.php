<?php
    include 'session.php';	
	
	$tb='';
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :
				try{
					$soNo = $_POST['soNo'];
					$saleDate = $_POST['saleDate'];
					$poNo = $_POST['poNo'];    
					$piNo = $_POST['piNo'];
					$deliveryDate = $_POST['deliveryDate'];    
					$smId = $_POST['smId'];
					$custId = (isset($_POST['custId'])? $_POST['custId'] : 0 );
					$shipToId = $_POST['shipToId'];

					$suppTypeId = $_POST['suppTypeId'];
					$stkTypeId = $_POST['stkTypeId'];
					$packageTypeId = $_POST['packageTypeId'];
					$priceTypeId = $_POST['priceTypeId'];
					$deliveryTypeId = $_POST['deliveryTypeId'];
					
					$containerLoadId = $_POST['containerLoadId']; 
					$shippingMarksId = $_POST['shippingMarksId'];
					$optTypeId = $_POST['optTypeId'];

					$creditTypeId = $_POST['creditTypeId'];					
					$payTypeCreditDays = $_POST['payTypeCreditDays'];
				
					$remark = $_POST['remark'];					
										
					$var = $saleDate;
					$var = str_replace('/', '-', $var);
					$saleDate = date("Y-m-d", strtotime($var));
					
					$var = $deliveryDate;
					$var = str_replace('/', '-', $var);
					$deliveryDate = date("Y-m-d", strtotime($var));

					//$pdo->beginTransaction();
					
					if($soNo==""){
						$soNo = 'SO-'.substr(str_shuffle(MD5(microtime())), 0, 7);
						$sql = "INSERT INTO `sale_header`
						(`soNo`, `poNo`, `piNo`, `saleDate`, `custId`, `shipToId`, `smId`, `revCount`, `deliveryDate`, `suppTypeId`, `stkTypeId`, `packageTypeId`, `priceTypeId`, `deliveryTypeId`, `shippingMarksId`, `containerLoadId`, `creditTypeId`, `remark`, `payTypeCreditDays`, `isClose`, `statusCode`, `createTime`, `createById`) 
						VALUES 
						(:soNo, :poNo, :piNo, :saleDate, :custId, :shipToId,  :smId, 0
						, :deliveryDate, :suppTypeId, :stkTypeId, :packageTypeId, :priceTypeId, :deliveryTypeId, :shippingMarksId, :containerLoadId, :creditTypeId, :remark, :payTypeCreditDays, 'N', 'B', now(), :createById) 
						";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':soNo', $soNo);
						$stmt->bindParam(':poNo', $poNo);
						$stmt->bindParam(':piNo', $piNo);
						$stmt->bindParam(':saleDate', $saleDate);
						$stmt->bindParam(':custId', $custId);
						$stmt->bindParam(':shipToId', $shipToId);
						$stmt->bindParam(':smId', $smId);
						$stmt->bindParam(':deliveryDate', $deliveryDate);
						$stmt->bindParam(':suppTypeId', $suppTypeId);
						$stmt->bindParam(':stkTypeId', $stkTypeId);
						$stmt->bindParam(':packageTypeId', $packageTypeId);
						$stmt->bindParam(':priceTypeId', $priceTypeId);
						$stmt->bindParam(':deliveryTypeId', $deliveryTypeId);
						$stmt->bindParam(':shippingMarksId', $shippingMarksId);
						$stmt->bindParam(':containerLoadId', $containerLoadId);
						$stmt->bindParam(':creditTypeId', $creditTypeId);
						$stmt->bindParam(':remark', $remark);
						$stmt->bindParam(':payTypeCreditDays', $payTypeCreditDays);
						$stmt->bindParam(':createById', $s_userId);	
						$stmt->execute();
					}else{
						$sql = "UPDATE `sale_header` SET `poNo`=:poNo, `piNo`=:piNo, `saleDate`=:saleDate, `custId`=:custId, `shipToId`=:shipToId, `smId`=:smId, `deliveryDate`=:deliveryDate, `suppTypeId`=:suppTypeId, `stkTypeId`=:stkTypeId, `packageTypeId`=:packageTypeId, `priceTypeId`=:priceTypeId, `deliveryTypeId`=:deliveryTypeId, `shippingMarksId`=:shippingMarksId, `containerLoadId`=:containerLoadId, `creditTypeId`=:creditTypeId, `remark`=:remark, `payTypeCreditDays`=:payTypeCreditDays
						,`statusCode`='C'
						, `updateTime`=NOW(), `updateById`=:updateById ";
						$sql .= "WHERE `soNo`=:soNo 
						";

						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':soNo', $soNo);
						$stmt->bindParam(':poNo', $poNo);
						$stmt->bindParam(':piNo', $piNo);
						$stmt->bindParam(':saleDate', $saleDate);
						$stmt->bindParam(':custId', $custId);
						$stmt->bindParam(':shipToId', $shipToId);
						$stmt->bindParam(':smId', $smId);
						$stmt->bindParam(':deliveryDate', $deliveryDate);
						$stmt->bindParam(':suppTypeId', $suppTypeId);
						$stmt->bindParam(':stkTypeId', $stkTypeId);
						$stmt->bindParam(':packageTypeId', $packageTypeId);
						$stmt->bindParam(':priceTypeId', $priceTypeId);
						$stmt->bindParam(':deliveryTypeId', $deliveryTypeId);
						$stmt->bindParam(':shippingMarksId', $shippingMarksId);
						$stmt->bindParam(':containerLoadId', $containerLoadId);
						$stmt->bindParam(':creditTypeId', $creditTypeId);
						$stmt->bindParam(':remark', $remark);
						$stmt->bindParam(':payTypeCreditDays', $payTypeCreditDays);
						$stmt->bindParam(':updateById', $s_userId);		
						
						$stmt->execute();
					}					
	
					//$pdo->commit();
					
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Success.', 'soNo' => $soNo));
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//Rollback the transaction.
					$pdo->rollBack();
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error : " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}	
				break;
				exit();
			
			case 'itemAdd' :
				try{
	
				    $soNo = $_POST['soNo'];			    
					$deliveryDate = $_POST['deliveryDateItem'];
				    $prodId = $_POST['prodId'];
					$qty = $_POST['qty'];
					$rollLengthId = (isset($_POST['rollLengthId'])? $_POST['rollLengthId'] : '' );
					$itemRemark = $_POST['itemRemark'];

				    $refItmId = $_POST['refItmId'];	
						
					$var = $deliveryDate;
					$var = str_replace('/', '-', $var);
					$deliveryDate = date("Y-m-d", strtotime($var));
					
					$pdo->beginTransaction();		

					$isInsertNewItem=true;

					if( $refItmId <> "" ){
						$sql = "SELECT qty FROM `sale_detail` WHERE id=:id 
						";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':id', $refItmId);	
						$stmt->execute();
						$row=$stmt->fetch();
						$oldQty=$row['qty'];
						$newQty=$oldQty-$qty;

						if($oldQty==$qty){
							//Update delivery datte, rollLengId, remark 
							$sql = "UPDATE `sale_detail` SET `deliveryDate`=:deliveryDate, `rollLengthId`=:rollLengthId, `remark`=:remark WHERE id=:id 
							";
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':deliveryDate', $deliveryDate);	
							$stmt->bindParam(':rollLengthId', $rollLengthId);	
							$stmt->bindParam(':remark', $itemRemark);	
							$stmt->bindParam(':id', $refItmId);	
							$stmt->execute();

							$isInsertNewItem=false;
						}else{
							if($newQty<=0){					
								header('Content-Type: application/json');
								$errors = "Error : "."incorrect Quantity.";
								echo json_encode(array('success' => false, 'message' => $errors));
								exit();
							}
							//Edit qty only
							$sql = "UPDATE `sale_detail` SET qty=:qty WHERE id=:id 
							";
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':qty', $newQty);	
							$stmt->bindParam(':id', $refItmId);	
							$stmt->execute();
						}						
					}

					if($isInsertNewItem){
						//insert product
					    $sql = "INSERT INTO `sale_detail`
						(`prodId`, `deliveryDate`, `qty`, `rollLengthId`, `remark`, `createTime`, `soNo`) 
						VALUES 
						(:prodId, :deliveryDate, :qty,:rollLengthId,:remark, now(), :soNo)
						";
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':prodId', $prodId);	
						$stmt->bindParam(':deliveryDate', $deliveryDate);	
						$stmt->bindParam(':qty', $qty);	
						$stmt->bindParam(':rollLengthId', $rollLengthId);	
						$stmt->bindParam(':remark', $itemRemark);	
						$stmt->bindParam(':soNo', $soNo);	
						$stmt->execute();
					}					
						
					$pdo->commit();
					
					header('Content-Type: application/json');
				    echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));				

				}catch(Exception $e){
					$pdo->rollBack();
					
					header('Content-Type: application/json');
					$errors = "Error on Data Verify. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;

			case 'getItemList' :
				try{	
					$soNo = $_POST['soNo'];

					$sql = "SELECT a.`id`, a.`prodId`, a.`deliveryDate`, a.`qty`,  a.`rollLengthId`, a.`remark`, a.`soNo`
					,b.code as prodCode, b.name as prodName, b.uomCode 
					, rl.name as rollLengthName 
					FROM `sale_detail` a
					LEFT JOIN product b on b.id=a.prodId
					LEFT JOIN product_roll_length rl ON rl.id=a.rollLengthId 
					WHERE 1 
					AND a.`soNo`=:soNo 
					ORDER BY a.id, prodCode, a.createTime ASC";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);	
					$stmt->execute();
					
					$rowCount=$stmt->rowCount();
					$jsonData = array();
					while($row=$stmt->fetch()){						
							$jsonData[] = $row;
					}
					
					header('Content-Type: application/json');				
					echo json_encode( array('success' => true, 'rowCount' => $rowCount, 'data' => json_encode($jsonData) ) );
				}catch(Exception $e){
					header('Content-Type: application/json');
				  $errors = "Error : " . $e->getMessage();
				  echo json_encode(array('success' => false, 'message' => $errors));
				} 
				break;	

			case 'getItem' :
				try{	
					$id = $_POST['id'];

					$sql = "SELECT a.`id`, a.`prodId`, a.`deliveryDate`, a.`qty`,  a.`rollLengthId`, a.`remark`, a.`soNo`
					,b.code as prodCode, b.name as prodName, b.uomCode 
					, rl.name as rollLengthName 
					FROM `sale_detail` a
					LEFT JOIN product b on b.id=a.prodId
					LEFT JOIN product_roll_length rl ON rl.id=a.rollLengthId 
					WHERE 1 
					AND a.`id`=:id 
					ORDER BY  a.id, a.createTime ASC";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);	
					$stmt->execute();
					
					$rowCount=$stmt->rowCount();
					$jsonData = $stmt->fetch();
					
					header('Content-Type: application/json');				
					echo json_encode( array('success' => true, 'rowCount' => $rowCount, 'data' => json_encode($jsonData) ) );
				}catch(Exception $e){
					header('Content-Type: application/json');
				  $errors = "Error : " . $e->getMessage();
				  echo json_encode(array('success' => false, 'message' => $errors));
				} 
				break;			

			case 'confirm' : 
				try{	
					//$session_userID=$_SESSION['userID'];
					
					$soNo = $_POST['soNo'];
					//$hdrTotal = $_POST['hdrTotal'];
					//$hdrVatAmount = $_POST['hdrVatAmount'];
					//$hdrNetTotal = $_POST['hdrNetTotal'];

					//We start our transaction.
					$pdo->beginTransaction();	
					
					//Query 1: Check Status for not gen running No.
					$sql = "SELECT * FROM sale_header WHERE soNo=:soNo AND statusCode='B' LIMIT 1";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}

					$sql = "UPDATE sale_header SET statusCode='C'   
						, confirmTime=now()
						, confirmById=?
						WHERE soNo=? ";
				    $stmt = $pdo->prepare($sql);
				    $stmt->execute(array(
							$s_userId,
							$soNo	
				        )
				    );
					
					    
				    //We've got this far without an exception, so commit the changes.
				    $pdo->commit();
					
				    //return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data Confirmed', 'soNo' => $soNo));
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
					$soNo = $_POST['soNo'];

					//Query 1: Check Status for not gen running No.
					$sql = "SELECT * FROM sale_header WHERE soNo=:soNo AND statusCode='C' LIMIT 1";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}
					
					//Query 1: UPDATE DATA
					$sql = "UPDATE sale_header SET statusCode='B'
							WHERE soNo=:soNo
							AND statusCode='C' 
						";
				    $stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
				    $stmt->execute();
					
				    //return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data rejected'));
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data rejecte. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;

			case 'delete' : 
				try{
				    $soNo = $_POST['soNo'];	
					
					//We start our transaction.
					$pdo->beginTransaction();
					
					//Query 1: Check Status for not gen running No.
					$sql = "SELECT soNo FROM sale_header WHERE soNo=:soNo AND statusCode<>'P' LIMIT 1";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$hdr = $stmt->fetch();	
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){		
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}	
					
					//DELETE Image
					//if($hdr['deliveryRemImg']<>""){
					//	@unlink('./dist/img/soDeli/'.$hdr['deliveryRemImg']);
					//}
					
					//Query 1: DELETE Detail
					$sql = "DELETE FROM `sale_detail` WHERE soNo=:soNo";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);	
					$stmt->execute();
					
					//Query 2: DELETE Header
					$sql = "DELETE FROM `sale_header` WHERE soNo=:soNo";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);	
					$stmt->execute();
					
					
					$sql = "DELETE FROM `sale_detail` WHERE soNo=:soNo";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);	
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
					$errors = "Error on Data Delete. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;

			case 'approve' :
				switch($s_userGroupCode){
					case 'it' : case 'admin' : case 'salesAdmin' : 
						break;
					default : 
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
						exit();
				}

				$soNo = $_POST['soNo'];

				//We will need to wrap our queries inside a TRY / CATCH block.
				//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
				try{
					//We start our transaction.
					$pdo->beginTransaction();
					//Query 1: Check Status for not gen running No.
					$sql = "SELECT hdr.*, cust.locationCode FROM sale_header hdr
							INNER JOIN customer cust ON cust.id=hdr.custId 
							WHERE soNo=:soNo AND hdr.statusCode='C' LIMIT 1";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}
					$row=$stmt->fetch();
					$locationCode=$row['locationCode'];
					$soNoNext = '';
					if($row['revCount']<>0){
						$soNoNext = $row['soNo'];
						//Query 2: UPDATE Header
						$sql = "UPDATE sale_header SET statusCode='P'   
							, approveTime=now()
							, approveById=?
							WHERE soNo=? ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(		
								$s_userId,
								$soNoNext	
							)
						);
					}else{//end if revised.
						$year = date('Y'); $name = ''; $prefix = ''; $cur_no=1;
						switch($locationCode){
							case 'L' : $name='saleLocal'; $prefix = 'SO'.date('y'); 
								break;
							case 'E' : $name='saleExport'; $prefix = 'SOE'.date('y'); 
								break;
							default :
								//return JSON
								header('Content-Type: application/json');
								echo json_encode(array('success' => false, 'message' => 'locationCode incorrect.'));
								exit();
						}	
						//Query 1: GET Next Doc No.
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
						$soNoNext = '';
						switch($locationCode){
							case 'L' : $soNoNext = $prefix . substr($next_no, -4);
								break;
							case 'E' : $soNoNext = $prefix . substr($next_no, -3);
								break;
							default :
						}
						
						//Query 2: UPDATE Header
						$sql = "UPDATE sale_header SET statusCode='P'   
							, soNo=?
							, approveTime=now()
							, approveById=?
							WHERE soNo=? ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array(		
								$soNoNext,
								$s_userId,
								$soNo	
							)
						);
						
						//Query 3: UPDATE Detail
						$sql = "UPDATE sale_detail SET soNo=? WHERE soNo=? ";
						$stmt = $pdo->prepare($sql);
						$stmt->execute(array($soNoNext,$soNo));
						
						//Query 4:  UPDATE doc running.
						$sql = "UPDATE doc_running SET cur_no=? WHERE year=? and name=?";
						$stmt = $pdo->prepare($sql);		
						$stmt->execute(array($cur_no, $year, $name));
					}//end if not revised
					
					
					$sloc=0;
					switch($locationCode){
						case 'L' : $sloc='8';
							break;
						case 'E' : $sloc='E';
							break;
						default :
					}
					
					
					//Query 5: UPDATE STK BAl
					$sql = "UPDATE stk_bal tmp
					INNER JOIN (SELECT sd.prodId, SUM(sd.qty) as sumQty
								FROM sale_detail sd  
								WHERE sd.soNo=:soNo 	
								GROUP BY sd.prodId) as x 
					SET tmp.sales=tmp.sales+x.sumQty
					WHERE tmp.prodId=x.prodId
					AND tmp.sloc=:sloc 		
					";
				    $stmt = $pdo->prepare($sql);
				    $stmt->bindParam(':soNo', $soNoNext);
				    $stmt->bindParam(':sloc', $sloc);
				    $stmt->execute();
					
					//Query 6: UPDATE STK BAl
					$sql = "INSERT INTO stk_bal (prodId, sloc, sales) 
							SELECT sd.prodId,:sloc, SUM(sd.qty) FROM sale_detail sd 
							WHERE sd.soNo=:soNo 
							AND sd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sb.sloc=:sloc2 )
							GROUP BY sd.prodId
							";
				    $stmt = $pdo->prepare($sql);
				    $stmt->bindParam(':soNo', $soNoNext);
				    $stmt->bindParam(':sloc', $sloc);
				    $stmt->bindParam(':sloc2', $sloc);
				    $stmt->execute();
				    
							
					//We've got this far without an exception, so commit the changes.
				    $pdo->commit();
					
				    //return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data approved', 'soNo' => $soNoNext));	
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//Rollback the transaction.
				    $pdo->rollBack();
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data approve. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;

			case 'revise' :
				try{	
					$soNo = $_POST['soNo'];
					$reason = $_POST['reason'];

					//Query 1: Check Status for not gen running No.
					$sql = "SELECT hdr.*, cust.locationCode FROM sale_header hdr
							INNER JOIN customer cust ON cust.id=hdr.custId 
							WHERE soNo=:soNo AND hdr.statusCode='P' LIMIT 1
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}
					$row=$stmt->fetch();
					$locationCode=$row['locationCode'];

					//We start our transaction.
					$pdo->beginTransaction();
					
					 
					//Log Header
					$sql = "INSERT INTO sale_rev_hdr  (`soNo`, `poNo`, `piNo`, `saleDate`, `custId`, `shipToId`, `smId`, `revCount`
					, `deliveryDate`, `suppTypeId`, `stkTypeId`, `packageTypeId`, `priceTypeId`, `deliveryTypeId`, `shippingMarksId`
					, `deliveryRem`, `containerLoadId`, `creditTypeId`, `remark`, `payTypeCreditDays`
					, `isClose`, `statusCode`, `createTime`, `createById`, `updateTime`, `updateById`, `confirmTime`, `confirmById`
					, `approveTime`, `approveById`, `logRemark`, `logTime`, `logById`) 
					SELECT *,:logRemark, NOW(), :s_userId FROM sale_header hdr 
					WHERE hdr.soNo=:soNo 
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':logRemark', $reason);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->bindParam(':s_userId', $s_userId);
					$stmt->execute();
					$logId = $pdo->lastInsertId();
					
					//Log Detail
					$sql = "INSERT INTO sale_rev_dtl (`id`, `prodId`, `deliveryDate`, `qty`, `rollLengthId`, `remark`, `createTime`, `soNo`
					, `logHdrId`)
					SELECT *,:logId FROM sale_detail dtl
					WHERE dtl.soNo=:soNo 
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':logId', $logId);
					$stmt->bindParam(':soNo', $soNo);	
					$stmt->execute();	
					
					//Query 1: UPDATE DATA
					$sql = "UPDATE sale_header SET statusCode='B'
					, revCount=revCount+1
					WHERE soNo=:soNo
					AND statusCode='P' 
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					

					$sloc=0;
					switch($locationCode){
						case 'L' : $sloc='8';
							break;
						case 'E' : $sloc='E';
							break;
						default :
					}
					//Query 5: UPDATE STK BAl	
					$sql = "UPDATE stk_bal tmp
					INNER JOIN (SELECT sd.prodId, -1*SUM(sd.qty) as sumQty
								FROM sale_detail sd  
								WHERE sd.soNo=:soNo 	
								GROUP BY sd.prodId) as x 
					SET tmp.sales=tmp.sales+x.sumQty
					WHERE tmp.prodId=x.prodId
					AND tmp.sloc=:sloc 		
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->bindParam(':sloc', $sloc);
					$stmt->execute();
					
					//Query 6: UPDATE STK BAl
					$sql = "INSERT INTO stk_bal (prodId, sloc, sales) 
							SELECT sd.prodId,:sloc, -1*SUM(sd.qty) FROM sale_detail sd 
							WHERE sd.soNo=:soNo 
							AND sd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sb.sloc=:sloc2 )
							GROUP BY sd.prodId
							";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->bindParam(':sloc', $sloc);
					$stmt->bindParam(':sloc2', $sloc);
					$stmt->execute();
					//Query 5: UPDATE STK BAl
					
					//We've got this far without an exception, so commit the changes.
					$pdo->commit();
					
					//return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data revised', 'soNo' => $soNo));
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data revise. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;


			case 'remove' :
				try{	
					$soNo = $_POST['soNo'];
					$userPassword = mysqli_real_escape_string($link,$_POST['pw']);

					// Encript Password
					$salt = "asdadasgfd";
					$hash_login_password = hash_hmac('sha256', $userPassword, $salt);

					//Query 1: Check Password is correct
					$sql = "SELECT userId FROM user WHERE userId=:userId AND userPassword=:userPassword LIMIT 1
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':userId', $s_userId);	
					$stmt->bindParam(':userPassword', $hash_login_password);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Password incorrect.'));
						exit();
					}

					//Query 1: Check SO is not Pick
					$sql = "SELECT pickNo FROM `picking` WHERE soNo=:soNo LIMIT 1
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);	
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count == 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Cannot remove used SO.'));
						exit();
					}

					//Query 1: Check Status for not gen running No.
					$sql = "SELECT hdr.*, cust.locationCode FROM sale_header hdr
							INNER JOIN customer cust ON cust.id=hdr.custId 
							WHERE soNo=:soNo AND hdr.statusCode='P' LIMIT 1
					";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}
					$row=$stmt->fetch();
					$locationCode=$row['locationCode'];

					//We start our transaction.
					$pdo->beginTransaction();
							
					//Query 1: UPDATE DATA
					$sql = "UPDATE sale_header SET statusCode='X'
					,`updateTime`=NOW()
					, `updateById`=:updateById
					WHERE soNo=:soNo
					AND statusCode='P' 
					";
				    $stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->bindParam(':updateById', $s_userId);
				    $stmt->execute();
					

					$sloc=0;
					switch($locationCode){
						case 'L' : $sloc='8';
							break;
						case 'E' : $sloc='E';
							break;
						default :
					}
					
					//Query 5: UPDATE STK BAl	
					$sql = "UPDATE stk_bal tmp
					INNER JOIN (SELECT sd.prodId, -1*SUM(sd.qty) as sumQty
								FROM sale_detail sd  
								WHERE sd.soNo=:soNo 	
								GROUP BY sd.prodId) as x 
					SET tmp.sales=tmp.sales+x.sumQty
					WHERE tmp.prodId=x.prodId
					AND tmp.sloc=:sloc 		
					";
				    $stmt = $pdo->prepare($sql);
				    $stmt->bindParam(':soNo', $soNo);
				    $stmt->bindParam(':sloc', $sloc);
				    $stmt->execute();
					
					//Query 6: UPDATE STK BAl
					$sql = "INSERT INTO stk_bal (prodId, sloc, sales) 
							SELECT sd.prodId,:sloc, -1*SUM(sd.qty) FROM sale_detail sd 
							WHERE sd.soNo=:soNo 
							AND sd.prodId NOT IN (SELECT sb.prodId FROM stk_bal sb WHERE sb.sloc=:sloc2 )
							GROUP BY sd.prodId
							";
				    $stmt = $pdo->prepare($sql);
				    $stmt->bindParam(':soNo', $soNo);
				    $stmt->bindParam(':sloc', $sloc);
				    $stmt->bindParam(':sloc2', $sloc);
				    $stmt->execute();
				    //Query 5: UPDATE STK BAl
				    
					
					//We've got this far without an exception, so commit the changes.
				    $pdo->commit();
					
				    //return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data revised', 'soNo' => $soNo));
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data revise. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;
			
			case 'itemDelete' :
				try{   
					$id = $_POST['id'];

					$sql = "DELETE FROM sale_detail WHERE id=:id ";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':id', $id);	
					$stmt->execute();
					
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Data deleted Complete.'));
				}catch(Exception $e){
					header('Content-Type: application/json');
					$errors = "Error on Data delete. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}	
				break;
			
			case 'close' : 
				//Check user roll.
				switch($s_userGroupCode){
					case 'admin' : case 'salesAdmin' : 
						break;
					default : 
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
						exit();
				}

				$soNo = $_POST['soNo'];

				//We will need to wrap our queries inside a TRY / CATCH block.
				//That way, we can rollback the transaction if a query fails and a PDO exception occurs.
				try{
					//We start our transaction.
					$pdo->beginTransaction();
					//Query 1: Check Status for not gen running No.
					$sql = "SELECT * FROM sale_header WHERE soNo=:soNo AND statusCode='P' LIMIT 1";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}
					
					//Query 1: UPDATE DATA
					$sql = "UPDATE sale_header sh SET sh.isClose='Y' WHERE soNo=:soNo 
						";
				    $stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
				    $stmt->execute();
						
					//We've got this far without an exception, so commit the changes.
				    $pdo->commit();
					
				    //return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'message' => 'Closed', 'soNo' => $soNo));	
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//Rollback the transaction.
				    $pdo->rollBack();
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data approve. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;
			case 'reopen' : 
				//Check user roll.
				switch($s_userGroupCode){
					case 'admin' : case 'salesAdmin' : 
						break;
					default : 
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Access Denied.'));
						exit();
				}

				try{	
					$soNo = $_POST['soNo'];
					//Query 1: Check Status for not gen running No.
					$sql = "SELECT * FROM sale_header WHERE soNo=:soNo AND statusCode='P' LIMIT 1";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
					$stmt->execute();
					$row_count = $stmt->rowCount();	
					if($row_count != 1 ){
						//return JSON
						header('Content-Type: application/json');
						echo json_encode(array('success' => false, 'message' => 'Status incorrect.'));
						exit();
					}
					
					//Query 1: UPDATE DATA
					$sql = "UPDATE sale_header SET isClose='N'
							WHERE soNo=:soNo
							AND statusCode='P' 
						";
				    $stmt = $pdo->prepare($sql);
					$stmt->bindParam(':soNo', $soNo);
				    $stmt->execute();
					
				    //return JSON
					header('Content-Type: application/json');
					echo json_encode(array('success' => true, 'soNo' => $soNo, 'message' => 'Data re-open completed'));
				} 
				//Our catch block will handle any exceptions that are thrown.
				catch(Exception $e){
					//return JSON
					header('Content-Type: application/json');
					$errors = "Error on Data rejecte. Please try again. " . $e->getMessage();
					echo json_encode(array('success' => false, 'message' => $errors));
				}
				break;

			default : 
				header('Content-Type: application/json');
				echo json_encode(array('success' => false, 'message' => 'Unknow action.'));				
		}
	}