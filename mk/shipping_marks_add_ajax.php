<?php
	include 'inc_helper.php'; 
    //include 'db.php';
	include 'session.php';
						
    //$id = $_POST['id'];
	$code = trim($_POST['code']);
	$typeCode = $_POST['typeCode'];
	$name = trim($_POST['name']);
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
		//if (file_exists('../images/shippingMarks/'.$curPhoto)) unlink('../images/shippingMarks/'.$curPhoto);
	
        $new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
        $path_upload = "../images/shippingMarks/".$new_picture_name;
        move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
    }
		
    $sql = "INSERT INTO `shipping_marks`(`code`, `name`, `typeCode`, `filePath`
	, `statusCode`, `createTime`, `createById`)  
	VALUES (	
	:code,:name,:typeCode,:photo
	,:statusCode, now(), :s_userId  
	)";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code); 
	$stmt->bindParam(':typeCode', $typeCode); 
	$stmt->bindParam(':name', $name); 
	$stmt->bindParam(':photo', $new_picture_name); 
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