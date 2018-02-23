<?php

include '../db/database.php';

$userName = mysqli_real_escape_string($link,$_POST['userName']);
$userPassword = mysqli_real_escape_string($link,$_POST['userPassword']);

// Encript Password
    $salt = "asdadasgfd";
    $hash_login_password = hash_hmac('sha256', $userPassword, $salt);
	//$hash_login_password ='f3597b30d60ecae02b38806634eef7c596ca25ee40521c09aef2a95464f3c594';

$sql = "SELECT * FROM wh_user WHERE (userName=? AND userPassword=?)";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ss", $userName,$hash_login_password);
mysqli_stmt_execute($stmt);
$result_user = mysqli_stmt_get_result($stmt);
if($result_user->num_rows >= 1){
    session_start();
	//force to create new password
	if($userPassword=="reset"){
		$_SESSION['reset']=1;
	}else{
		//session_destroy()
	}
	
    $row_user = mysqli_fetch_array($result_user, MYSQLI_ASSOC);
    $_SESSION['userId'] = $row_user['userId'];
    $_SESSION['userName'] = $row_user['userName'];
    header('Content-Type: application/json');
    echo json_encode(array('status' => 'success'));
      
} else {
    header('Content-Type: application/json');
    $errors = "Username or Password incorrect.". mysqli_error($link);
    echo json_encode(array('status' => 'danger', 'message' => $errors));
    
    
}
mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);