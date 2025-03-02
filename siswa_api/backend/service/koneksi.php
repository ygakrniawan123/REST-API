<?php
$host = "localhost";
$user = "root";
$pw = "";
$db_name = "siswa_api";


$conn = mysqli_connect($host, $user, $pw, $db_name);

// melakukan cek jika tidak terkonek ke database
if(!$conn){
    echo "koneksi gagal";
}


?>