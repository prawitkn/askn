<?php
//  Day7 1:25:37

    include '../db/database.php';
    
    $prodID = $_GET['prodID'];
    
    foreach ($prodID as $id) {
        $sql = "DELETE FROM product WHERE prodID = '$id' ";
        $result = mysqli_query($link, $sql);
 //       alert("deleted");
    }

 // Day 7 1:28:54 copied from insert_Cust.php
       if ($result) {
 
      header('Content-Type: application/json');
      echo json_encode(array('status' => 'success', 'message' => 'Data Deleted.'));
   } else {
      header('Content-Type: application/json');
      $errors = "Error on Data Deletion. Please try again. " . mysqli_error($link);
      echo json_encode(array('status' => 'danger', 'message' => $errors));
 //   echo " Cannot Insert.";
 //   echo mysqli_error($link);
}
