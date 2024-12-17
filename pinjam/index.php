<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil semua data peminjaman
$loans = $peminjaman_collection->find([], ['sort' => ['tanggal_pinjam' => -1]]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjaman - Sistem Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container table-page mt-4">
        <div class="form-card">
            <div class="card-body">
                <!-- Header -->
                <div class="form-header">
                    <h2 class="form-title">
                        <i class="fas fa-hand-holding"></i>
                        Data Peminjaman
                    </h2>
                    <p class="text-muted">Kelola data peminjaman buku perpustakaan</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mini-stat-card">
                                <div class="stat-icon blue">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $peminjaman_collection->countDocuments(); ?></h3>
                                    <p>Total Peminjaman</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-card">
                                <div class="stat-icon orange">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $peminjaman_collection->countDocuments(['status' => 'dipinjam']); ?></h3>
                                    <p>Sedang Dipinjam</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-card">
                                <div class="stat-icon green">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $peminjaman_collection->countDocuments(['status' => 'dikembalikan']); ?></h3>
                                    <p>Sudah Dikembalikan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-container mb-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="filter-buttons">
                                <div class="search-box">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Cari peminjaman...">
                                    <i class="fas fa-search search-icon"></i>
                                </div>
                                <select class="form-select" id="statusFilter">
                                    <option value="">Semua Status</option>
                                    <option value="dipinjam">Dipinjam</option>
                                    <option value="dikembalikan">Dikembalikan</option>
                                </select>
                                <select class="form-select" id="sortBy">
                                    <option value="terbaru">Urutkan: Terbaru</option>
                                    <option value="terlama">Terlama</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Pinjam</th>
                                <th>Judul Buku</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($loans as $loan) {
                                $tanggal_pinjam = $loan['tanggal_pinjam']->toDateTime()->format('d/m/Y');
                                $tanggal_kembali = $loan['tanggal_kembali']->toDateTime()->format('d/m/Y');
                                
                                echo "<tr>";
                                echo "<td>{$no}</td>";
                                echo "<td>{$loan['kode_pinjam']}</td>";
                                echo "<td>{$loan['judul_buku']}</td>";
                                echo "<td>{$loan['nama_peminjam']}</td>";
                                echo "<td>{$tanggal_pinjam}</td>";
                                echo "<td>{$tanggal_kembali}</td>";
                                echo "<td><span class='status-badge status-{$loan['status']}'>{$loan['status']}</span></td>";
                                echo "<td>
                                    <div class='action-buttons'>
                                        <a href='detail_pinjam.php?id={$loan['_id']}' class='btn-action btn-info'>
                                            <i class='fas fa-eye'></i>
                                        </a>
                                    </div>
                                </td>";
                                echo "</tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Back Button -->
                <div class="mt-4">
                    <a href="../main.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 