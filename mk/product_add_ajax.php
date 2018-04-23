<?php
	include 'inc_helper.php'; 
    //include 'db.php';
	include 'session.php';
						
//`id`, `code`, `catCode`, `name`, `name2`, `uomCode`, `ratioPack`, `packUomCode`
//, `sourceTypeCode`, `appCode`, `isFg`, `isWip`, `photo`, `price`, `description`, `statusCode`
    //$id = $_POST['id'];
	$code = trim($_POST['code']);
	$catCode = $_POST['catCode'];
	$name = trim($_POST['name']);
	$name2 = trim($_POST['name2']);
	$uomCode = trim($_POST['uomCode']);
	$sourceTypeCode = $_POST['sourceTypeCode']; 
	$appCode = $_POST['appCode'];
	$price = $_POST['price'];
	$appCode = $_POST['appCode'];
	$description = $_POST['description'];	
	$statusCode = (isset($_POST['statusCode'])? 'A' : 'I' );
	
	$curPhoto = $_POST['curPhoto'];
		
	$new_picture_name=$curPhoto;
	 
	 //Check Duplicate
	 $sql = "SELECT * FROM `product` WHERE `code`=:code LIMIT 1 ";     
	 $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code); 
    $stmt->execute();
	if($stmt->rowCount()>=1){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Duplicate data.'));
		exit;
	}	
	
	if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
		// If the old picture already exists, delete it
		//if (file_exists('../images/product/'.$curPhoto)) unlink('../images/product/'.$curPhoto);
	
        $new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
        $path_upload = "../images/product/".$new_picture_name;
        move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
    }
		
    $sql = "INSERT INTO `product`(`code`, `catCode`, `name`, `name2`, `uomCode`
	, `sourceTypeCode`, `appCode`, `photo`, `price`, `description`, `statusCode`, `createTime`, `createById`)  
	VALUES (	
	:code,:catCode,:name,:name2,:uomCode,:sourceTypeCode,:appCode,:photo,:price,:description
	,:statusCode, now(), :s_userId  
	)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code); 
	$stmt->bindParam(':catCode', $catCode); 
	$stmt->bindParam(':name', $name); 
	$stmt->bindParam(':name2', $name2); 
	$stmt->bindParam(':uomCode', $uomCode); 
	$stmt->bindParam(':sourceTypeCode', $sourceTypeCode); 
	$stmt->bindParam(':appCode', $appCode); 
	$stmt->bindParam(':photo', $new_picture_name); 
	$stmt->bindParam(':price', $price); 
	$stmt->bindParam(':description', $description); 
	$stmt->bindParam(':statusCode', $statusCode); 
	$stmt->bindParam(':s_userId', $s_userId);
 
    if ($stmt->execute()) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try new username. " . $pdo->errorInfo();
      echo json_encode(array('success' => false, 'message' => $errors));
	}
?>