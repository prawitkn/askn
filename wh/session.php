<?php

    session_start();
    if (!isset($_SESSION['userId'])){
        header("Location: login.php");
    }
    
    include 'appConfig.php';	
	include '../db/db.php';
		
	if(!isset($_COOKIE["loginWh"])){
		header("Location: logout.php");
	}
	
    $s_userId=$_SESSION['userId'];
    $qry_user = "SELECT * FROM wh_user WHERE userId='$s_userId'";
    $result_user = mysqli_query($link,$qry_user);
    if ($result_user) {	
        $row_user = mysqli_fetch_array($result_user,MYSQLI_ASSOC);
						
        $s_userFullname = $row_user['userFullname'];
        $s_userPicture = $row_user['userPicture'];
		$s_username = $row_user['userName'];
		$s_userGroupCode = $row_user['userGroupCode'];
		$s_userDeptCode = $row_user['userDeptCode'];		
        
        //$s_admin = $row_user['userName'];
		mysqli_free_result($result_user);  
		
		//Set Login 
		//setcookie("loginWh", "1", time()+3600);	//3600=1Hour; 1800=30Min; 60=1Min
		
		$qry_user = "UPDATE wh_user SET loginStatus=1, lastLoginTime=NOW() WHERE userId='$s_userId'";
		$result_user = mysqli_query($link,$qry_user);        
        
    }else{
		header("Location: login.php");
	}