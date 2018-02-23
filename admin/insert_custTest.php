<?php

    include '../db/database.php';
   
    $custName = $_POST['custName'];
    $custAddr = $_POST['custAddr'];
    $custUsername = $_POST['custUsername'];
    $custPassword = $_POST['custPassword'];
    $custEmail = $_POST['custEmail'];
    $custTel = $_POST['custTel'];
    
    
    
    $sql = "INSERT INTO customer (custName, custAddr, custUsername, custPassword, custEmail, custTel) VALUES ('$custName', '$custAddr', '$custUsername', '$custPassword', '$custEmail', '$custTel')";
 
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
