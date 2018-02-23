<?php
include 'session.php'; //Global Var => $s_userID,$s_username,$s_userFullname,$s_userGroupCode
	
	$search_sloc = $_POST['search_sloc'];
	$result = mysqli_query($link, "SELECT * FROM wh_sloc where sloc='$search_sloc' AND statusCode='A' ");	

	if ($result) {
		//$rows = mysqli_fetch_assoc($result);
		header('Content-Type: application/json');
		$jsonData = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$jsonData[] = $row;
		} 					   
		//echo json_encode($jsonData);
		//$rows = json_encode($result);
        echo '{"success": true, "rows": '.json_encode($jsonData).'}';
		//echo json_encode(array('status' => 'success', 'rows' => json_encode($rows)));
   } else {
      header('Content-Type: application/json');
      $errors = "Username or Password incorrect. " . mysqli_error($link);
	  echo '{"success": danger, message => '.$errors.' }';
      //echo json_encode(array('status' => 'danger', 'message' => $errors));
	}
?>


