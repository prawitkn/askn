<?php

    include '../db/database.php';
   
    $prodTypeID = $_POST['prodTypeID'];
    $prodTypeName = $_POST['prodTypeName'];
     
    
    
    
    
    $sql = "INSERT INTO product_type (prodTypeID, prodTypeName) VALUES ('$prodTypeID', '$prodTypeName')";
 
    $result = mysqli_query($link, $sql);
 
    if ($result) {
 //     header("Location: product_type.php");
 //     echo "Finished Insert.";
      header('Content-Type: application/json');
      echo json_encode(array('status' => 'success', 'message' => 'Data Inserted Complete.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Insertion. Please try again. " . mysqli_error($link);
      echo json_encode(array('status' => 'danger', 'message' => $errors));
 //   echo " Cannot Insert.";
 //   echo mysqli_error($link);
}
