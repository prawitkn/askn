<?php

    session_start();
    if (!isset($_SESSION['userId'])){
        header("Location: login.php");
    }
	
	include 'appConfig.php';	
	include '../db/db.php';	
    
	//ALTER TABLE `wh_user` ADD `loginStatus` INT NOT NULL DEFAULT '0' AFTER `statusCode`, ADD `lastLoginTime` DATETIME NOT NULL AFTER `loginStatus`;
	if(!isset($_COOKIE["loginMk"])){
		header("Location: login.php");
	}
	
    $s_userId=$_SESSION['userId'];
    $sql = "SELECT u.`userId`, u.`userFullname`, u.`userGroupCode`, u.`smId`, u.`userEmail`, u.`userTel`, u.`userPicture`, u.`statusCode` 
	, sm.code as smCode 
	FROM user u
	LEFT JOIN salesman sm on sm.id=u.smId 
	WHERE u.userId=:s_userId ";
	
   // $result_user = mysqli_query($link,$qry_user);
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':s_userId', $s_userId);	
    if ($stmt->execute()) {
        $row_user = $stmt->fetch(); // mysqli_fetch_array($result_user,MYSQLI_ASSOC);
	//$s_userId = $row_user['userId'];
        $s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		//$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_smCode = $row_user['smCode'];
		$s_smId = $row_user['smId'];
		
        
        //$s_admin = $row_user['userName'];
        
        //mysqli_free_result($result_user);  
		$stmt->closeCursor();
		
		//Set Login 
		setcookie("loginMk", "1", time()+1200);	//3600=1Hour; 1800=30Min; 60=1Min
		
		$sql = "UPDATE user SET lastLoginTime=NOW() WHERE userId=:s_userId ";		
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':s_userId', $s_userId);	  
        
    }