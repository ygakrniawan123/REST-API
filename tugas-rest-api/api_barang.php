<?php
include "koneksi.php";

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$inrput = json_decode(file_get_contents('php://input'), true);


switch ($method){
    // method GET
    case 'GET';
    if(isset($_GET['Id'])){
        $Id = $_GET['Id'];
        $result = $conn->query("SELECT * FROM barang_api WHERE Id = $Id");
        $data = $result->fetch_assoc();
        echo json_encode($data);
    }else {
        $result = $conn->query("SELECT * FROM barang_api");
        $barangData = [];
        while ($row = $result->fetch_assoc()) {
            $barangData[] = $row;
        }
    }
    echo json_encode($barangData);
    break;
    // method POST
    case 'POST';
    $Nama_barang = $input['Nama_barang'];
    $Harga = $input['Harga'];
    $Stok = $input['Stok'];
    $result = $conn->query("INSERT INTO barang_api (Nama_barang, Harga, Stok) VALUES ('$Nama_barang', '$Harga', $Stok)");
    echo json_encode(["message" => "berhasil tambah data barang"]);
    break;
    // method PUT
    case 'PUT';
    $Id  = $_GET['Id'];
    $Nama_barang = $input['Nama_barang'];
    $Harga = $input['Harga'];
    $Stok = $input['Stok'];
    $result = $conn->query("UPDATE barang_api SET Nama_barang = '$Nama_barang', Harga = '$Harga', Stok = $Stok WHERE Id = $Id");
    echo json_encode(["message" => "berhasil update data barang"]);
    break;


    
    // method DELETE
    case 'DELETE';
    $Id = $_GET['Id'];
    $result = $conn->query("DELETE FROM barang_api WHERE Id = $Id");
    echo json_encode(["message" => "berhasil hapus data barang"]);
    break;

    default:
    echo json_encode(["message" => "gagal request method"]);
    break;

}
$conn->close();


?>