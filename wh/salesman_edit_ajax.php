<?php
	include 'inc_helper.php'; 
    //include 'db.php';
	include 'session.php';
	 
    $id = $_POST['id'];
	$name = $_POST['name'];
	$surname = $_POST['surname'];
	$positionName = $_POST['positionName'];
	$mobileNo = $_POST['mobileNo'];
	$email = $_POST['email']; 
	$statusCode = $_POST['statusCode'];
	$statusCode=explode(',', $statusCode);
	 
	$sc="X";
	foreach($statusCode as $check) {
			if($check=="A"){
				$sc = "A";
			}
    }

    $sql = "UPDATE `salesman` SET 
			  `name`='$name' 
			, `surname`='$surname'
			, `positionName`='$positionName'
			, `mobileNo`='$mobileNo'
			, `email`='$email'
			, `statusCode`='$sc'	
			WHERE ID=$id 
			";
 
    $result = mysqli_query($link, $sql);
 
    if ($result) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Update Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Update. Please try again. " . mysqli_error($link);
      echo json_encode(array('success' => false, 'message' => $errors));
	}
?>