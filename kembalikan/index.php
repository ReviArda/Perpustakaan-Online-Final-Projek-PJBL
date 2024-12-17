<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil semua data pengembalian
$returns = $kembalikan_collection->find([], ['sort' => ['tanggal_kembali' => -1]]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengembalian - Sistem Perpustakaan</title>
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
                        <i class="fas fa-undo"></i>
                        Data Pengembalian
                    </h2>
                    <p class="text-muted">Kelola data pengembalian buku perpustakaan</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-container mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mini-stat-card">
                                <div class="stat-icon blue">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $kembalikan_collection->countDocuments(); ?></h3>
                                    <p>Total Pengembalian</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-card">
                                <div class="stat-icon green">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $kembalikan_collection->countDocuments(['denda' => 0]); ?></h3>
                                    <p>Tepat Waktu</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mini-stat-card">
                                <div class="stat-icon orange">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $kembalikan_collection->countDocuments(['denda' => ['$gt' => 0]]); ?></h3>
                                    <p>Terlambat</p>
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
                                    <input type="text" class="form-control" id="searchInput" placeholder="Cari pengembalian...">
                                    <i class="fas fa-search search-icon"></i>
                                </div>
                                <select class="form-select" id="dendaFilter">
                                    <option value="">Semua Status</option>
                                    <option value="tepat">Tepat Waktu</option>
                                    <option value="terlambat">Terlambat</option>
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
                                <th>Kode Kembali</th>
                                <th>Judul Buku</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($returns as $return) {
                                $tanggal_pinjam = $return['tanggal_pinjam']->toDateTime()->format('d/m/Y');
                                $tanggal_kembali = $return['tanggal_kembali']->toDateTime()->format('d/m/Y');
                                $denda = number_format($return['denda'], 0, ',', '.');
                                
                                echo "<tr>";
                                echo "<td>{$no}</td>";
                                echo "<td>{$return['kode_kembali']}</td>";
                                echo "<td>{$return['judul_buku']}</td>";
                                echo "<td>{$return['nama_peminjam']}</td>";
                                echo "<td>{$tanggal_pinjam}</td>";
                                echo "<td>{$tanggal_kembali}</td>";
                                echo "<td>Rp {$denda}</td>";
                                echo "<td>
                                    <div class='action-buttons'>
                                        <a href='detail_kembali.php?id={$return['_id']}' class='btn-action btn-info'>
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