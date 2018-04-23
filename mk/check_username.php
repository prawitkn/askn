<?php

include '../db/database.php';
$custUsername = $_GET['custUsername'];


$sql = "SELECT custID FROM customer WHERE custUsername='$custUsername'";
$result = mysqli_query($link, $sql);
$row = mysqli_num_rows($result);

if ($row >= 1) {

     header('Content-Type: application/json');
      echo json_encode(array('status' => 'active', 'message' => 'This Customer Name is already in use. Please differentiate by adding more characters or create new name.'));
    
} 