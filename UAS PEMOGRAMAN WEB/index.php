<?php
// index.php - Main dashboard for ERP Product Management
session_start();
require_once 'koneksi.php';

// Fetch all products using PDO Prepared Statement to follow the rule:
// "Prepared Statement wajib digunakan untuk semua query"
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ERP - Kelola Produk</title>
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
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn-success {
            background-color: #2ec4b6;
            border-color: #2ec4b6;
        }
        .btn-success:hover {
            background-color: #20a396;
            border-color: #20a396;
        }
        .btn-warning {
            background-color: #ff9f1c;
            border-color: #ff9f1c;
            color: white;
        }
        .btn-warning:hover {
            background-color: #e78b0f;
            border-color: #e78b0f;
            color: white;
        }
        .badge-stock {
            font-size: 0.9rem;
            padding: 0.5em 0.75em;
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

    <!-- Main Content -->
    <div class="container">
        
        <!-- Display Session Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-1">Daftar Produk</h3>
                <p class="text-muted mb-0">Kelola inventaris, harga, dan stok produk Anda.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="tambah.php" class="btn btn-primary px-4 py-2 shadow-sm d-inline-flex align-items-center">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Produk Baru
                </a>
            </div>
        </div>

        <!-- Product Table Card -->
        <div class="card p-3 mb-5">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th scope="col" class="text-center" style="width: 60px;">No</th>
                            <th scope="col" style="width: 150px;">Kode Produk</th>
                            <th scope="col">Nama Produk</th>
                            <th scope="col" style="width: 180px;">Harga</th>
                            <th scope="col" class="text-center" style="width: 120px;">Stok</th>
                            <th scope="col" class="text-center" style="width: 280px;">Update Stok</th>
                            <th scope="col" class="text-center" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php $no = 1; foreach ($products as $product): ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace px-2 py-1.5"><?= htmlspecialchars($product['kode_produk']) ?></span>
                                    </td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($product['nama_produk']) ?></td>
                                    <td class="text-primary fw-bold">Rp <?= number_format($product['harga'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-stock <?= $product['stock'] > 10 ? 'bg-info-subtle text-info-emphasis' : ($product['stock'] > 0 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-danger-subtle text-danger-emphasis') ?> rounded-pill border">
                                            <?= $product['stock'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Form to Update Stock (Requires POST method) -->
                                        <form action="update_stok.php" method="POST" class="d-flex align-items-center justify-content-center">
                                            <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                            <div class="input-group input-group-sm" style="max-width: 220px;">
                                                <input type="number" name="jumlah" class="form-control text-center" min="1" value="1" required>
                                                <button type="submit" name="aksi" value="tambah" class="btn btn-success" title="Tambah Stok">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                                <button type="submit" name="aksi" value="kurang" class="btn btn-warning" title="Kurangi Stok">
                                                    <i class="bi bi-dash-lg"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <!-- Edit and Delete actions -->
                                        <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit Produk">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus Produk" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus produk <?= htmlspecialchars($product['nama_produk']) ?>?');">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    Belum ada produk terdaftar. Silakan tambah produk baru terlebih dahulu.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
