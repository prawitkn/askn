<?php

include '../db/database.php';

$userName = mysqli_real_escape_string($link,$_POST['userName']);
$userPassword = mysqli_real_escape_string($link,$_POST['userPassword']);

// Encript Password
$salt = "asdadasgfd";
$hash_login_password = hash_hmac('sha256', $userPassword, $salt);
//$hash_login_password ='f3597b30d60ecae02b38806634eef7c596ca25ee40521c09aef2a95464f3c594';

//$qry_user = "UPDATE user SET loginStatus=0
//WHERE (TIME_TO_SEC(NOW()) - TIME_TO_SEC( `lastLoginTime`))/60 > 20  ";
$qry_user = "UPDATE user SET loginStatus=0
WHERE TIME_TO_SEC(TIMEDIFF(NOW(), `lastLoginTime`))/60 > 20  ";
$result_user = mysqli_query($link,$qry_user);	

$sql = "SELECT * FROM user WHERE (userName=? AND userPassword=?) LIMIT 1";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ss", $userName,$hash_login_password);
mysqli_stmt_execute($stmt);
$result_user = mysqli_stmt_get_result($stmt);
if($result_user->num_rows >= 1){
    session_start();
     
	$row_user = mysqli_fetch_array($result_user, MYSQLI_ASSOC);
	
	if($row_user['loginStatus']==0){
		$s_userId=$row_user['userId'];
		$_SESSION['userId'] = $s_userId;
		$_SESSION['userName'] = $row_user['userName'];
		
		//Set Login 
		setcookie("loginMk", "1", time()+1200);	//3600=1Hour; 1800=30Min; 60=1Min
			
		$qry_user = "UPDATE user SET lastLoginTime=NOW(), loginStatus=1 WHERE userId='$s_userId'";
		$result_user = mysqli_query($link,$qry_user);
		
		//force to create new password
		if($userPassword=="reset"){
			$_SESSION['reset']=1;
		}else{
			//session_destroy()
		}
		
		header('Content-Type: application/json');
		echo json_encode(array('status' => 'success'));
	}else{
		//header('Content-Type: application/json');
		//echo json_encode(array('status' => 'success'));
		header('Content-Type: application/json');
		$errors = "Another user is logged in this username.". mysqli_error($link);
		echo json_encode(array('status' => 'danger', 'message' => $errors));
	}
} else {
    header('Content-Type: application/json');
    $errors = "Username or Password incorrect.". mysqli_error($link);
    echo json_encode(array('status' => 'danger', 'message' => $errors)); 
}
mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);