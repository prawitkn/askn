<?php

    session_start();
    if (!isset($_SESSION['userId'])){
        header("Location: login.php");
    }
    
	include '../db/db.php';
	
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
        
        
    }else{
		header("Location: login.php");
	}