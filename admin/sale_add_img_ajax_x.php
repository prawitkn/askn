<?php
include 'inc_helper.php';  
include 'session.php';	
	
try{
			
	$soNo = $_POST['soNo'];
	$custCode = $_POST['custCode'];
	
	$pdo->beginTransaction();
	
	if (isset($_FILES['soDeliFile'])) {
		$myFile = $_FILES['soDeliFile'];
		$fileCount = count($myFile["name"]);

		for ($i = 0; $i < $fileCount; $i++) {
			//$myFile["name"][$i]
			//$myFile["tmp_name"][$i]
			//$myFile["type"][$i]
			//$myFile["size"][$i]
			//$myFile["error"][$i]	
			
			 // Upload Picture
			 $soImgName = "";
			if (is_uploaded_file($myFile['tmp_name'])){
				$soImgName = 'soDeli_'.$custCode.'_'.uniqid().".".pathinfo(basename($myFile['name']), PATHINFO_EXTENSION);
				$path_upload = "./dist/img/soDeli/".$soImgName;
				move_uploaded_file($myFile['tmp_name'], $path_upload);			
			}
			
			//Update data
			$sql = " 
			UPDATE `sale_header` SET `deliveryRemImg`=:soImgName 
			WHERE soNo=:soNo 
			";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':soImgName', $soImgName);
			$stmt->bindParam(':soNo', $soNo);
			$stmt->execute();		
		}
		//end for.
		
		//Commit
		$pdo->commit();
		
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'message' => 'Data Update Completed.'));
	}else{
		//No upload file.
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'There is no file upload.'));
	}
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
