<?php

    include 'session.php';	
	
   
    $code = $_POST['code'];
    $name = $_POST['name'];
    
 // Check user name duplication?
    $sql = "SELECT code FROM wh_user_dept WHERE code=:code ";
    //$result_user = mysqli_query($link, $sql_user);
    //$is_user = mysqli_num_rows($result_user);
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':code', $code);
	$stmt->execute();		
    if ($stmt->rowCount() >= 1){
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Duplicate user production department code, Please try new user group. " . mysqli_error($link);
      echo json_encode(array('status' => 'danger', 'message' => $errors));  
      exit;    
    }   
            
    $sql = "INSERT INTO `wh_user_dept` (`code`, `name`, `statusCode`, `createTime`, `createById`)"
	. " VALUES (:code,:name,'A',NOW(),:createById)";
	$stmt = $pdo->prepare($sql);	
	$stmt->bindParam(':code', $code);
	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':createById', $s_userID);
 
    if ($stmt->execute()) {
      header('Content-Type: application/json');
      echo json_encode(array('status' => true, 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try again. " . mysqli_error($link);
      echo json_encode(array('status' => false, 'message' => $errors));
}
