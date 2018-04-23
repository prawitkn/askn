<?php
	include 'inc_helper.php'; 
    //include 'db.php';
	include 'session.php';

try{	
    $id = $_POST['id'];
	$code = $_POST['code'];
	$typeCode = $_POST['typeCode'];
	$name = $_POST['name'];
	$statusCode = (isset($_POST['statusCode'])? 'A' : 'I' );
	
	
	$curPhoto = $_POST['curPhoto'];
		
	$new_picture_name=$curPhoto;
	 	
	 // Upload Picture
    if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
		// If the old picture already exists, delete it
		if (file_exists('../images/shippingMarks/'.$curPhoto)) unlink('../images/shippingMarks/'.$curPhoto);
	
        $new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
        $path_upload = "../images/shippingMarks/".$new_picture_name;
        move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
    }
	
    $sql = "UPDATE `shipping_marks` SET `code`=:code
	, `typeCode`=:typeCode
	, `name`=:name
	, `filePath`=:new_picture_name
	, `statusCode`=:statusCode
	WHERE id=:id
	"; 
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code);
	$stmt->bindParam(':typeCode', $typeCode);
	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':new_picture_name', $new_picture_name);
	$stmt->bindParam(':statusCode', $statusCode);
	$stmt->bindParam(':id', $id);
	$stmt->execute();
	
	header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Update Complete.'));
}catch(Exception $e){
	header('Content-Type: application/json');
  $errors = "Error on Data Verify. Please try again. " . $e->getMessage();
  echo json_encode(array('success' => false, 'message' => $errors));
} 
?>