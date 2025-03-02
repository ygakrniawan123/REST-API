<?php
include "service/koneksi.php"; // koneksi ke database 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    // method GET untuk menampilkan data 
    case 'GET':
        if (isset($_GET['id'])) { // mengecek apakah ada id 
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM siswa WHERE id = ?"); // Gunakan prepared statement untuk keamanan
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM siswa");
            $siswaData = [];
            while ($row = $result->fetch_assoc()) { // melakukan perulangan untuk mengisi data array kosong
                $siswaData[] = $row; // Perbaikan: menambahkan data ke array
            }
            echo json_encode($siswaData);
        }
        break;

    // method POST untuk tambah dan update data
    case 'POST':
        $action = $_GET['action'] ?? NULL; // membuat variable ACTION untuk mengetahui action pada parameter
        
        if (empty($action)) {
            echo json_encode(["status" => "error", "message" => "action kosong"]);
            exit();
        }
        
        if ($action === 'tambah') {
            $nama = $_POST['nama'] ?? null;
            $jk = $_POST['jk'] ?? null;
            $alamat = $_POST['alamat'] ?? null;

            if (!empty($_FILES['foto']['name'])) {
                $foto = time() . "." . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['foto']['tmp_name'], "images/" . $foto);
            } else {
                $foto = "default.jpg"; // Gambar default jika tidak upload
            }

            // Insert ke database
            $stmt = $conn->prepare("INSERT INTO siswa(nama, jk, alamat, foto) VALUES(?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nama, $jk, $alamat, $foto);
            $success = $stmt->execute();
            echo json_encode(["status" => $success ? "success" : "error", "message" => $success ? "Data berhasil ditambahkan" : "Gagal menambah data"]);
        }
        
        elseif ($action === 'update') {
            $id = $_GET['id'] ?? null;
            $nama = $_POST['nama'] ?? null;
            $jk = $_POST['jk'] ?? null;
            $alamat = $_POST['alamat'] ?? null;

            if (empty($id)) {
                echo json_encode(["status" => "error", "message" => "id kosong"]);
                exit();
            }

            // Cek foto lama
            $fotoStmt = $conn->prepare("SELECT foto FROM siswa WHERE id = ?");
            $fotoStmt->bind_param("i", $id);
            $fotoStmt->execute();
            $resultFoto = $fotoStmt->get_result();
            $row = $resultFoto->fetch_assoc();
            $fotoLama = $row['foto'];

            if (!empty($_FILES['foto']['name'])) {
                $foto = time() . "." . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['foto']['tmp_name'], "images/" . $foto);
                
                // Hapus foto lama hanya jika bukan default
                if ($fotoLama && $fotoLama !== "default.jpg" && file_exists("images/$fotoLama")) {
                    unlink("images/" . $fotoLama);
                }
            } else {
                $foto = $fotoLama; // Pakai foto lama jika tidak diupdate
            }

            // Update data
            $updateStmt = $conn->prepare("UPDATE siswa SET nama = ?, jk = ?, alamat = ?, foto = ? WHERE id = ?");
            $updateStmt->bind_param("ssssi", $nama, $jk, $alamat, $foto, $id);
            $success = $updateStmt->execute();
            echo json_encode(["status" => $success ? "success" : "error", "message" => $success ? "Data berhasil diupdate" : "Gagal update data"]);
        }
        break;

    // method DELETE untuk hapus data
    case 'DELETE':
        $id = $_GET['id'] ?? null; // Perbaikan: menangani jika id tidak diberikan
        
        if (empty($id)) {
            echo json_encode(["status" => "error", "message" => "id kosong"]);
            exit();
        }

        // Ambil foto lama sebelum hapus
        $fotoStmt = $conn->prepare("SELECT foto FROM siswa WHERE id = ?");
        $fotoStmt->bind_param("i", $id);
        $fotoStmt->execute();
        $resultFoto = $fotoStmt->get_result();
        $row = $resultFoto->fetch_assoc();
        $fotoLama = $row['foto'];

        // Hapus data dari database
        $stmt = $conn->prepare("DELETE FROM siswa WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();

        if ($success && $stmt->affected_rows > 0) {
            // Hapus foto lama hanya jika bukan default
            if ($fotoLama && $fotoLama !== "default.jpg" && file_exists("images/$fotoLama")) {
                unlink("images/$fotoLama");
            }
            echo json_encode(["status" => "success", "message" => "Berhasil hapus data siswa"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Tidak ada data yang dihapus"]);
        }
        break;
}
?>
