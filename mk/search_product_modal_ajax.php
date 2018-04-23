<?php
	include 'session.php';
	
	$search_org_code = $_POST['search_org_code'];
	$search_fullname = $_POST['search_fullname'];
	$result = mysqli_query($link, "SELECT * FROM product where prodName like '%$search_fullname%' limit 100");

	

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


