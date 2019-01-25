<?php
    include 'session.php';	
	
	if(!isset($_POST['action'])){		
		header('Content-Type: application/json');
		echo json_encode(array('status' => 'danger', 'message' => 'No action.'));
	}else{
		switch($_POST['action']){
			case 'add' :				
				try{	
					$closingDate = $_POST['closingDate'];					
					$closingDate = str_replace('/', '-', $closingDate);
					$closingDateYmd = date("Y-m-d",strtotime($closingDate));
					$dateFromYmd=$closingDateYmd;

					// Check duplication?
					$sql = "SELECT `id`, `closingDate`, `createTime`, `createUserId`, `updateTime`, `updateUserId` FROM `stk_closing` WHERE closingDate=:closingDate LIMIT 1 ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':closingDate', $closingDateYmd);
					$stmt->execute();
					if ($stmt->rowCount() == 1){
					  header('Content-Type: application/json');
						$errors = "Error on Data Insertion. Please try new closingDate. ";
						echo json_encode(array('status' => 'warning', 'message' => $errors));
						exit();
					}else{ 								
						
						//We start our transaction.
						$pdo->beginTransaction();

			          	$sql = "
			          	CREATE TEMPORARY TABLE tmpStock (
			          		`prodId` int(11) NOT NULL,
							  `sloc` varchar(10) NOT NULL,
							  `openAcc` decimal(10,2) NOT NULL,
							  `openTrans` decimal(10,2) NOT NULL,
							  `onway` decimal(10,2) NOT NULL,
							  `receive` decimal(10,2) NOT NULL,
							  `sent` decimal(10,2) NOT NULL,
							  `return` decimal(10,2) NOT NULL,
							  `delivery` decimal(10,2) NOT NULL,
							  `balance` decimal(10,2) NOT NULL,
							  `book` decimal(10,2) NOT NULL,
					      	PRIMARY KEY (`prodId`,`sloc`)
					    )";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();

						$sql = "
				          INSERT INTO tmpStock (prodId, sloc)
				          SELECT prd.id, sl.code 
				          FROM product prd
				          CROSS JOIN sloc sl ON sl.code IN ('8','E')
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();

						//Open
						$sql = "UPDATE tmpStock hdr
				         ,(SELECT td.prodId, td.sloc, td.balance as sumQty FROM stk_closing_detail td 
				          				WHERE td.hdrId=(SELECT th.id FROM stk_closing th 
				          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1) 
				          				) as tmp 
				          SET hdr.openAcc=tmp.sumQty 
				          WHERE hdr.prodId=tmp.prodId AND hdr.sloc=tmp.sloc 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();

						//Onway
						$sql = "UPDATE tmpStock hdr
				         ,(SELECT itm.prodCodeId, sh.toCode, SUM(itm.qty) as sumQty FROM product_item itm 
				          				INNER JOIN send_detail sd ON sd.prodItemId=itm.prodItemId  
				         				INNER JOIN send sh ON sh.sdNo=sd.sdNo AND sh.statusCode='P' AND sh.rcNo IS NULL 
				          				GROUP BY itm.prodCodeId, sh.toCode
				          				) as tmp 
				          SET hdr.onway=tmp.sumQty 
				          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.toCode 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();

						/*
						//Receive
						$sql = "UPDATE tmpStock hdr
				         ,(SELECT itm.prodCodeId, th.toCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
				          				INNER JOIN receive_detail td ON td.prodItemId=itm.prodItemId  
				         				INNER JOIN receive th ON th.rcNo=td.rcNo AND th.statusCode='P' 
				         					AND th.receiveDate > (SELECT th.closingDate FROM stk_closing th 
				          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
				          				GROUP BY itm.prodCodeId, th.toCode
				          				) as tmp 
				          SET hdr.receive=tmp.sumQty 
				          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();

						//Sent
						$sql = "UPDATE tmpStock hdr
				         ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty 
				         				FROM product_item itm 
				          				INNER JOIN send_detail td ON td.prodItemId=itm.prodItemId  
				         				INNER JOIN send th ON th.sdNo=td.sdNo AND th.statusCode='P' 
				         					AND th.sendDate > (SELECT th.closingDate FROM stk_closing th 
				          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
				          				GROUP BY itm.prodCodeId, th.fromCode
				          				) as tmp 
				          SET hdr.sent=tmp.sumQty 
				          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();

						//return
						$sql = "UPDATE tmpStock hdr
				         ,(SELECT itm.prodCodeId, th.fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
				          				INNER JOIN rt_detail td ON td.prodItemId=itm.prodItemId  
				         				INNER JOIN rt th ON th.rtNo=td.rtNo AND th.statusCode='P' 
				         					AND th.returnDate > (SELECT th.closingDate FROM stk_closing th 
				          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
				          				GROUP BY itm.prodCodeId, th.fromCode
				          				) as tmp 
				          SET hdr.return=tmp.sumQty 
				          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();

						//delivery
						$sql = "UPDATE tmpStock hdr
				         ,(SELECT itm.prodCodeId, cust.locationCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
				          				INNER JOIN delivery_detail td ON td.prodItemId=itm.prodItemId  
				         				INNER JOIN delivery_header th ON th.doNo=td.doNo AND th.statusCode='P' 
				         					AND th.deliveryDate > (SELECT th.closingDate FROM stk_closing th 
				          								WHERE th.statusCode='A' AND th.closingDate<='$dateFromYmd' LIMIT 1)
				         				INNER JOIN sale_header shd ON shd.soNo=th.soNo 
				         				INNER JOIN customer cust ON cust.id=shd.custId 
				          				GROUP BY itm.prodCodeId, cust.locationCode 
				          				) as tmp 
				          SET hdr.delivery=tmp.sumQty 
				          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();
						*/





						//Receive
						$sql = "UPDATE tmpStock hdr
				         ,(SELECT itm.prodCodeId, th.toCode as fromCode, SUM(itm.qty) as sumQty FROM product_item itm 
				          				INNER JOIN receive_detail td ON td.prodItemId=itm.prodItemId AND td.statusCode='A'   
				         				INNER JOIN receive th ON th.rcNo=td.rcNo AND th.statusCode='P' 
				         					
				          				GROUP BY itm.prodCodeId, th.toCode
				          				) as tmp 
				          SET hdr.balance=tmp.sumQty 
				          WHERE hdr.prodId=tmp.prodCodeId AND hdr.sloc=tmp.fromCode 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();



						//delete
						/*$sql = "UPDATE tmpStock 
						SET `balance`=`openAcc`+`openTrans`+`receive`-`sent`-`return`-`delivery`
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();
						*/

						//delete
						$sql = "DELETE FROM tmpStock 
						WHERE `openAcc`=0 AND `openTrans`=0 AND `onway`=0
						AND `receive`=0 AND `sent`=0 AND `return`=0 AND `delivery`=0 
						AND `balance`=0 AND `book`=0 
			          	";
			          	$stmt = $pdo->prepare($sql);		
						$stmt->execute();
				
						$sql = "INSERT INTO stk_closing (`closingDate`
						, `statusCode`, `createTime`, `createUserId`) 
						VALUES (:closingDate, 'A', NOW(), :createUserId)
						";						 
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':closingDate', $closingDateYmd);
						$stmt->bindParam(':createUserId', $s_userId);
						$stmt->execute();
						//Get last insert ID 
						$id=$pdo->lastInsertId(); 

						$sql = "INSERT INTO stk_closing_detail (`hdrId`
						, `sloc`, `prodId`, `balance`) 
						SELECT :hdrId, hd.sloc, hd.prodId, hd.balance 
						FROM tmpStock hd 
						WHERE hd.balance<>0 
						";						 
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':hdrId', $id);
						$stmt->execute();

						//We've got this far without an exception, so commit the changes.
						$pdo->commit();	

						header('Content-Type: application/json');
						echo json_encode(array('status' => 'success', 'message' => 'Data Inserted Complete.'));						
					}						
				}catch(Exception $e){
					//Rollback the transaction.
					$pdo->rollBack();

					header('Content-Type: application/json');
				  $errors = "Error : " . $e->getMessage();
				  echo json_encode(array('status' => 'danger', 'message' => $errors));
				} 			
				break;
			
			case 'setActive' :				
				try{					
					$id = $_POST['id'];
					$statusCode = $_POST['statusCode'];	
					
					$sql = "UPDATE stk_closing SET statusCode=:statusCode, updateTime=NOW(), updateUserId=:updateUserId WHERE id=:id ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':statusCode', $statusCode);
					$stmt->bindParam(':id', $id);
					$stmt->bindParam(':updateUserId', $s_userId);
					$stmt->execute();	
					if ($stmt->execute()) {
					  header('Content-Type: application/json');
					  echo json_encode(array('status' => 'success', 'message' => 'Data Updated Complete.'));
					} else {
					  header('Content-Type: application/json');
					  $errors = "Error on Data Update. Please try new data. " . $pdo->errorInfo();
					  echo json_encode(array('status' => 'danger', 'message' => $errors));
					}
				}catch(Exception $e){
					header('Content-Type: application/json');
				  $errors = "Error : " . $e->getMessage();
				  echo json_encode(array('status' => 'danger', 'message' => $errors));
				} 					
				break;
			case 'remove' :
				try{					
					$id = $_POST['id'];	
					
					$sql = "UPDATE stk_closing SET statusCode='X', updateTime=NOW(), updateUserId=:updateUserId WHERE id=:id ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':id', $id);
					$stmt->bindParam(':updateUserId', $s_userId);	
					$stmt->execute();

					 header('Content-Type: application/json');
					  echo json_encode(array('status' => 'success', 'message' => 'Data Removed Complete.'));
				}catch(Exception $e){
					header('Content-Type: application/json');
				  $errors = "Error : " . $e->getMessage();
				  echo json_encode(array('status' => 'danger', 'message' => $errors));
				}
				break;
			case 'delete' :
				try{					
					$id = $_POST['id'];					
					
					//We start our transaction.
					$pdo->beginTransaction();

					$sql = "DELETE FROM stk_closing_detail WHERE hdrId=:hdrId ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':hdrId', $id);
					$stmt->execute();

					$sql = "DELETE FROM stk_closing WHERE id=:id ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':id', $id);
					$stmt->execute();

					//We've got this far without an exception, so commit the changes.
					$pdo->commit();	

					header('Content-Type: application/json');
					  echo json_encode(array('status' => 'success', 'message' => 'Data Delete Complete.'));
					
				}catch(Exception $e){
					//Rollback the transaction.
					$pdo->rollBack();

					header('Content-Type: application/json');
				  $errors = "Error : " . $e->getMessage();
				  echo json_encode(array('status' => 'danger', 'message' => $errors));
				}
				break;	

			default : 
				header('Content-Type: application/json');
				echo json_encode(array('status' => 'danger', 'message' => 'Unknow action.'));				
		}
	}