<?php
	include 'inc_helper.php'; 
    //include 'db.php';
	include 'session.php';

try{	
//`id`, `code`, `catCode`, `name`, `name2`, `uomCode`, `ratioPack`, `packUomCode`
//, `sourceTypeCode`, `appCode`, `isFg`, `isWip`, `photo`, `price`, `description`, `statusCode`
    $id = $_POST['id'];
	$code = $_POST['code'];
	$catCode = $_POST['catCode'];
	$name = $_POST['name'];
	$name2 = $_POST['name2'];
	$uomCode = $_POST['uomCode'];
	$sourceTypeCode = $_POST['sourceTypeCode']; 
	$appCode = $_POST['appCode'];
	$price = $_POST['price'];
	$appCode = $_POST['appCode'];
	$description = $_POST['description'];	
	$statusCode = (isset($_POST['statusCode'])? 'A' : 'I' );
	
	
	$curPhoto = $_POST['curPhoto'];
		
	$new_picture_name=$curPhoto;
	 
	
	/*$fileName = $_FILES['inputFile']['name'];
    //$fileExt = pathinfo($_FILES["inputFile"]["name"], PATHINFO_EXTENSION);
    $filePath = "files/".$fileName;
    if (move_uploaded_file($_FILES["inputFile"]["tmp_name"], $filePath)) {
        echo "Upload success";
    } else {
        echo "Upload failed";
    }*/
	
	
	 // Upload Picture
    if (is_uploaded_file($_FILES['inputFile']['tmp_name'])){
		// If the old picture already exists, delete it
		if (file_exists('../images/product/'.$curPhoto)) unlink('../images/product/'.$curPhoto);
	
        $new_picture_name = 'prod_'.uniqid().".".pathinfo(basename($_FILES['inputFile']['name']), PATHINFO_EXTENSION);
        $path_upload = "../images/product/".$new_picture_name;
        move_uploaded_file($_FILES['inputFile']['tmp_name'], $path_upload);        
    }
	
//`id`, `code`, `catCode`, `name`, `name2`, `uomCode`, `ratioPack`, `packUomCode`
//, `sourceTypeCode`, `appCode`, `isFg`, `isWip`, `photo`, `price`, `description`, `statusCode`

    $sql = "UPDATE `product` SET `code`=:code
	, `catCode`=:catCode
	, `name`=:name
	, `name2`=:name2
	, `uomCode`=:uomCode
	, `sourceTypeCode`=:sourceTypeCode
	, `appCode`=:appCode
	, `photo`=:new_picture_name
	, `price`=:price
	, `description`=:description
	, `statusCode`=:statusCode
	WHERE id=:id
	"; 
    $stmt = $pdo->prepare($sql);
	$stmt->bindParam(':code', $code);
	$stmt->bindParam(':catCode', $catCode);
	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':name2', $name2);
	$stmt->bindParam(':uomCode', $uomCode);
	$stmt->bindParam(':sourceTypeCode', $sourceTypeCode);
	$stmt->bindParam(':appCode', $appCode);
	$stmt->bindParam(':new_picture_name', $new_picture_name);
	$stmt->bindParam(':price', $price);
	$stmt->bindParam(':description', $description);
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