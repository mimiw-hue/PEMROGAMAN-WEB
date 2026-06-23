<?php
// hapus.php - Delete product by ID from GET parameter
session_start();
require_once 'koneksi.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    $_SESSION['error'] = "ID produk tidak valid atau tidak disertakan.";
    header("Location: index.php");
    exit;
}

try {
    // 1. Fetch details of the product to get its code/name for the success message
    $stmt = $pdo->prepare("SELECT kode_produk, nama_produk FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = "Produk tidak ditemukan atau sudah dihapus.";
        header("Location: index.php");
        exit;
    }

    // 2. Delete the product using Prepared Statement
    $deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $deleteResult = $deleteStmt->execute([':id' => $id]);

    if ($deleteResult) {
        $_SESSION['success'] = "Produk '{$product['nama_produk']}' ({$product['kode_produk']}) berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus produk dari database.";
    }
} catch (\PDOException $e) {
    $_SESSION['error'] = "Kesalahan database: " . $e->getMessage();
}

// Redirect back to dashboard as required:
// "Setelah proses berhasil, redirect menggunakan header("Location:index.php")."
header("Location: index.php");
exit;
?>
