<?php

    include '../db/database.php';
 if (!$link)
 {
     echo ' Server Not Connected';
 }
 
 if(!mysqli_select_db($link, 'website'))
 {
     echo "DataBase NOT Connected.";
 }       
   
 $prodTypeID = $_POST['prodTypeID'];
 $prodTypeName = $_POST['prodTypeName'];
  
 $sql = "INSERT INTO product_type (prodTypeID, prodTypeName) VALUES ('$prodTypeID', '$prodTypeName')";
 
  //    $result = mysqli_query($link, $sql);
  
 if(!mysqli_query($link, $sql))
 {
     echo 'Not Inserted';
     echo mysqli_error($link);
 }
 else {
     echo 'Inserted already.'; 
 }
   $result = mysqli_query($link, $sql);
     
//TEST whether can get result.
if (!$result) {
    echo 'No value for result';
    
    
   // header("Location: product_type.php");
    
}
