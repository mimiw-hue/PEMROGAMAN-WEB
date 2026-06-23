<?php
// tambah.php - Add new product
session_start();
require_once 'koneksi.php';

$error = '';
$success = '';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $kode_produk = trim($_POST['kode_produk'] ?? '');
    $nama_produk = trim($_POST['nama_produk'] ?? '');
    $harga = $_POST['harga'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // Simple validation
    if (empty($kode_produk) || empty($nama_produk) || $harga === '' || $stock === '') {
        $error = "Semua field harus diisi.";
    } elseif ($harga < 0 || $stock < 0) {
        $error = "Harga dan Stok tidak boleh bernilai negatif.";
    } else {
        try {
            // Check if kode_produk already exists using Prepared Statement
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE kode_produk = :kode");
            $checkStmt->execute([':kode' => $kode_produk]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $error = "Kode produk '$kode_produk' sudah digunakan. Silakan gunakan kode lain.";
            } else {
                // Insert into database using Prepared Statement
                $insertStmt = $pdo->prepare("INSERT INTO products (kode_produk, nama_produk, harga, stock) VALUES (:kode, :nama, :harga, :stock)");
                $insertResult = $insertStmt->execute([
                    ':kode' => $kode_produk,
                    ':nama' => $nama_produk,
                    ':harga' => $harga,
                    ':stock' => $stock
                ]);

                if ($insertResult) {
                    $_SESSION['success'] = "Produk '$nama_produk' ($kode_produk) berhasil ditambahkan!";
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Gagal menyimpan produk ke database.";
                }
            }
        } catch (\PDOException $e) {
            $error = "Kesalahan database: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - ERP Sederhana</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-3 mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-box-seam me-2 fs-3"></i>
                <span class="fw-bold tracking-tight">ERP Sederhana</span>
            </a>
        </div>
    </nav>

    <!-- Form Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <!-- Back Link -->
                <div class="mb-3">
                    <a href="index.php" class="text-decoration-none text-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                </div>

                <div class="card p-4 mb-5">
                    <h3 class="fw-bold text-dark mb-4 text-center">Tambah Produk Baru</h3>

                    <!-- Display Inline Error Message if exists -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                                <div><?= htmlspecialchars($error) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="tambah.php" method="POST">
                        <div class="mb-3">
                            <label for="kode_produk" class="form-label fw-semibold">Kode Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase font-monospace" id="kode_produk" name="kode_produk" 
                                   value="<?= htmlspecialchars($kode_produk ?? '') ?>" placeholder="Contoh: PRD001" required maxlength="20">
                            <div class="form-text">Kode produk harus unik dan maksimal 20 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="nama_produk" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_produk" name="nama_produk" 
                                   value="<?= htmlspecialchars($nama_produk ?? '') ?>" placeholder="Contoh: Kertas A4" required maxlength="100">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga" class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="harga" name="harga" 
                                           value="<?= htmlspecialchars($harga ?? '') ?>" placeholder="10000" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="stock" name="stock" 
                                       value="<?= htmlspecialchars($stock ?? '0') ?>" placeholder="0" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary py-2.5">
                                <i class="bi bi-save me-1"></i> Simpan Produk
                            </button>
                            <a href="index.php" class="btn btn-light py-2.5 text-secondary">Batal</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
