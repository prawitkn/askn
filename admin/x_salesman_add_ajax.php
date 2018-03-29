<?php
	include 'inc_helper.php'; 
    //include 'db.php';
	
	include 'session.php';		

	$name = $_POST['name'];
	$surname = $_POST['surname'];
	$positionName = $_POST['positionName'];
	$mobileNo = $_POST['mobileNo'];
	$email = $_POST['email'];
	 
	 //Check Duplicate
	 $sql = "SELECT * FROM `salesman` WHERE `name`='$name' AND `surname`='$surname' LIMIT 1 "; 
    $result = mysqli_query($link, $sql);
	if(mysqli_num_rows($result)>=1){
		header('Content-Type: application/json');
		echo json_encode(array('success' => false, 'message' => 'Duplicate data.'));
		exit;
	}
		
    $sql = "INSERT INTO `salesman`(`name`, `surname`, `positionName`, `mobileNo`, `email`, `statusCode`)  "  
         . " VALUES ('$name', '$surname', '$positionName', '$mobileNo', '$email', 'A')";
 
    $result = mysqli_query($link, $sql);
 
    if ($result) {
      header('Content-Type: application/json');
      echo json_encode(array('success' => true, 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try new username. " . mysqli_error($link);
      echo json_encode(array('success' => false, 'message' => $errors));
	}
?>