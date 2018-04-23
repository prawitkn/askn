<?php

include '../db/database.php';

$id = $_GET['id'];

//delee image
$sql_imag = "SELECT userPicture FROM user WHERE userID='$id'";
$result_img = mysqli_query($link, $sql_img);
@unlink('./dist/img/'.$img_name[0]);

$sql = "DELETE FROM user WHERE userID='$id'";

$result = mysqli_query($link, $sql);

if ($result) {
    header("Location: user.php");
}
        

