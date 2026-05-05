<?php
require_once 'config/database.php';

// Validasi parameter ID dari GET 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=" . urlencode("ID tidak valid."));
    exit;
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    header("Location: index.php?error=" . urlencode("ID tidak valid."));
    exit;
}

// Cek keberadaan data di database 
$cek = $conn->prepare("SELECT id_kategori, nama_kategori FROM kategori WHERE id_kategori = ?");
$cek->bind_param('i', $id);
$cek->execute();
$res = $cek->get_result();

if ($res->num_rows === 0) {
    $cek->close();
    header("Location: index.php?error=" . urlencode("Kategori tidak ditemukan."));
    exit;
}

$data = $res->fetch_assoc();
$nama = $data['nama_kategori'];
$cek->close();

// Proses delete dengan prepared statement 
$stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

// Cek affected_rows untuk memastikan berhasil
if ($stmt->affected_rows > 0) {
    $stmt->close();
    header("Location: index.php?pesan=" . urlencode("Kategori \"$nama\" berhasil dihapus."));
    exit;
} else {
    $stmt->close();
    header("Location: index.php?error=" . urlencode("Gagal menghapus kategori. Silakan coba lagi."));
    exit;
}
?>
