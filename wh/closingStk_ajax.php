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
		
					// Check duplication?
					$sql = "SELECT `id`, `closingDate`, `createTime`, `createUserId`, `updateTime`, `updateUserId` FROM `stk_closing` WHERE closingDate=:closingDate LIMIT 1 ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':closingDate', $closingDateYmd);
					$stmt->execute();
					if ($stmt->rowCount() == 1){
					  header('Content-Type: application/json');
						$errors = "Error on Data Insertion. Please try new username. ";
						echo json_encode(array('status' => 'warning', 'message' => $errors));
						exit();
					}else{ 								
						
						//We start our transaction.
						$pdo->beginTransaction();

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
						FROM stk_bal hd 
						WHERE hd.balance<>0 
						";						 
						$stmt = $pdo->prepare($sql);	
						$stmt->bindParam(':hdrId', $id);
						$stmt->execute();


						$sql = "DELETE FROM stk_bal WHERE balance=0 
						";						 
						$stmt = $pdo->prepare($sql);	
						$stmt->execute();

						$sql = "UPDATE stk_bal SET open=balance 
						";						 
						$stmt = $pdo->prepare($sql);	
						$stmt->execute();

						$sql = "UPDATE `stk_bal` SET `produce`=0,`onway`=0,`onwayReturn`=0,`receive`=0,`wip`=0,`send`=0,`returnRecv`=0,`sales`=0,`delivery`=0; 
						";						 
						$stmt = $pdo->prepare($sql);	
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
					$Id = $_POST['Id'];
					$StatusId = $_POST['StatusId'];	
					
					$sql = "UPDATE ".$tb." SET StatusId=:StatusId, UpdateTime=NOW(), UpdateUserId=:UpdateUserId WHERE id=:Id ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':StatusId', $StatusId);
					$stmt->bindParam(':Id', $Id);
					$stmt->bindParam(':UpdateUserId', $s_userId);
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
					$Id = $_POST['Id'];	
					
					$sql = "UPDATE ".$tb." SET StatusId=3, UpdateTime=NOW(), UpdateUserId=:UpdateUserId WHERE Id=:Id ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':Id', $Id);
					$stmt->bindParam(':UpdateUserId', $s_userId);	
					if ($stmt->execute()) {
					  header('Content-Type: application/json');
					  echo json_encode(array('status' => 'success', 'message' => 'Data Removed Complete.'));
					} else {
					  header('Content-Type: application/json');
					  $errors = "Error on Data Remove. Please try new data. " . $pdo->errorInfo();
					  echo json_encode(array('status' => 'danger', 'message' => $errors));
					}
				}catch(Exception $e){
					header('Content-Type: application/json');
				  $errors = "Error : " . $e->getMessage();
				  echo json_encode(array('status' => 'danger', 'message' => $errors));
				}
				break;
			case 'delete' :
				try{					
					$Id = $_POST['Id'];
					$StatusId = $_POST['StatusId'];	
					
					
					$sql = "DELETE FROM ".$tb." WHERE Id=:Id ";
					$stmt = $pdo->prepare($sql);	
					$stmt->bindParam(':Id', $Id);
					if ($stmt->execute()) {
					  header('Content-Type: application/json');
					  echo json_encode(array('status' => 'success', 'message' => 'Data Delete Complete.'));
					} else {
					  header('Content-Type: application/json');
					  $errors = "Error on Data Delete. " . $pdo->errorInfo();
					  echo json_encode(array('status' => 'danger', 'message' => $errors));
					}
				}catch(Exception $e){
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