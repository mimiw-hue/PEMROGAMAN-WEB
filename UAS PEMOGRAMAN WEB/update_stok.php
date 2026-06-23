<?php
// update_stok.php - Adjust stock values (add or subtract)
session_start();
require_once 'koneksi.php';

// Check if request is submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Metode request tidak valid.";
    header("Location: index.php");
    exit;
}

$id = $_POST['id'] ?? '';
$jumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : 0;
$aksi = $_POST['aksi'] ?? '';

// Basic validation
if (empty($id) || $jumlah <= 0 || !in_array($aksi, ['tambah', 'kurang'])) {
    $_SESSION['error'] = "Input data tidak valid.";
    header("Location: index.php");
    exit;
}

try {
    // 1. Fetch current product stock using Prepared Statement
    $stmt = $pdo->prepare("SELECT kode_produk, nama_produk, stock FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();

    if (!$product) {
        $_SESSION['error'] = "Produk tidak ditemukan.";
        header("Location: index.php");
        exit;
    }

    $currentStock = intval($product['stock']);
    $newStock = $currentStock;

    if ($aksi === 'tambah') {
        $newStock = $currentStock + $jumlah;
        
        // Update stock using Prepared Statement
        $updateStmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
        $updateResult = $updateStmt->execute([
            ':stock' => $newStock,
            ':id'    => $id
        ]);

        if ($updateResult) {
            $_SESSION['success'] = "Stok produk '{$product['nama_produk']}' ({$product['kode_produk']}) berhasil ditambahkan {$jumlah} unit! (Total stok saat ini: {$newStock})";
        } else {
            $_SESSION['error'] = "Gagal memperbarui stok.";
        }

    } elseif ($aksi === 'kurang') {
        // Validate if stock is enough to subtract
        if ($currentStock < $jumlah) {
            $_SESSION['error'] = "Gagal mengurangi stok! Stok saat ini ({$currentStock}) kurang dari jumlah yang ingin dikurangi ({$jumlah}).";
        } else {
            $newStock = $currentStock - $jumlah;
            
            // Update stock using Prepared Statement
            $updateStmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
            $updateResult = $updateStmt->execute([
                ':stock' => $newStock,
                ':id'    => $id
            ]);

            if ($updateResult) {
                $_SESSION['success'] = "Stok produk '{$product['nama_produk']}' ({$product['kode_produk']}) berhasil dikurangi {$jumlah} unit! (Total stok saat ini: {$newStock})";
            } else {
                $_SESSION['error'] = "Gagal memperbarui stok.";
            }
        }
    }
} catch (\PDOException $e) {
    $_SESSION['error'] = "Kesalahan database: " . $e->getMessage();
}

// Redirect back to dashboard:
// "Setelah proses berhasil, redirect menggunakan header("Location:index.php")."
header("Location: index.php");
exit;
?>
