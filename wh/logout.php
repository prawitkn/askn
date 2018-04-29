<?php

include 'session.php';

if (session_destroy()) {
	$sql ="UPDATE wh_user SET loginStatus=0 WHERE userId=:s_userId ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':s_userId', $s_userId);
	$stmt->execute();
	
	if(isset($_COOKIE["loginCk"])){
		setcookie("loginWh", "1", time()-3600);
	}
	
    header("Location: login.php");
}
