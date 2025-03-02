<?php
$host = "localhost";
$user = "root";
$pw = "";
$db_name = "toko_api";
// jika berhasil konek ke database
$conn = mysqli_connect($host, $user, $pw, $db_name);


if(!$conn){
    echo "tidak konek";
}


?>