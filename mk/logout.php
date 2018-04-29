<?php

include 'session.php';

if (session_destroy()) {
	$sql ="UPDATE user SET loginStatus=0 WHERE userId=:s_userId ";
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':s_userId', $s_userId);
	$stmt->execute();
	
	if(isset($_COOKIE["loginMk"])){
		setcookie("loginMk", "1", time()-1200);
	}
	
    header("Location: login.php");
}
