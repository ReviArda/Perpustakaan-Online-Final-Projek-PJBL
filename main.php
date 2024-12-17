<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
header("Location: login/login.php");
exit;
}

// Koneksi MongoDB
require 'vendor/autoload.php';
$client = new MongoDB\Client("mongodb://localhost:27017"); //Noted: Website ini tidak dapat dijalankan bila device tidak terinstall MongoDB & PHP module MongoDB

// Collections
$buku_collection = $client->admin_book->buku;
$peminjaman_collection = $client->admin_book->peminjaman;
$kembalikan_collection = $client->admin_book->kembalikan_buku;

// Statistik
$totalBuku = $buku_collection->countDocuments();
$bukuDipinjam = $peminjaman_collection->countDocuments(['status' => 'dipinjam']);
$totalPeminjaman = $peminjaman_collection->countDocuments() + $kembalikan_collection->countDocuments();

// Ambil data aktivitas terbaru
$recentPeminjaman = $peminjaman_collection->find([], [
'sort' => ['tanggal_pinjam' => -1],
'limit' => 3
])->toArray();

$recentPengembalian = $kembalikan_collection->find([], [
'sort' => ['tanggal_kembali' => -1],
'limit' => 2
])->toArray();
?>

<!DOCTYPE html>  <!--Noted: Website ini tidak dapat dijalankan bila device tidak terinstall MongoDB & PHP module MongoDB-->       
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Sistem Perpustakaan Online</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-book-reader me-2"></i> Perpustakaan Online
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="main.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="buku/index.php">
                        <i class="fas fa-book"></i> Data Buku
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pinjam/index.php">
                        <i class="fas fa-hand-holding"></i> Peminjaman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kembalikan/index.php">
                        <i class="fas fa-undo"></i> Pengembalian
                    </a>
                </li>
            </ul>
            <div class="d-flex">
                <div class="user-profile me-2">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <a href="login/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Content -->
<div class="container">
    <!-- Welcome Section -->
    <div class="welcome-section slide-in">
        <div class="welcome-pattern"></div>
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="welcome-content">
                    <span class="welcome-badge">Dashboard Admin</span>
                    <h2 class="display-4 fw-bold mb-3">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
                    <p class="lead mb-4">Kelola perpustakaan dengan mudah dan efisien.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="book-animation">
                    <div class="book">
                        <div class="book-page"></div>
                        <div class="book-page"></div>
                        <div class="book-page"></div>
                    </div>
                    <div class="book-shelf"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row slide-up">
        <div class="col-md-4">
            <div class="stat-card primary">
                <div class="stat-card-content">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3 class="stat-card-number"><?php echo $totalBuku; ?></h3>
                            <p class="stat-card-title">Total Buku</p>
                        </div>
                    </div>
                    <div class="stat-card-chart">
                        <div class="stat-card-label">Koleksi perpustakaan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card success">
                <div class="stat-card-content">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3 class="stat-card-number"><?php echo $bukuDipinjam; ?></h3>
                            <p class="stat-card-title">Sedang Dipinjam</p>
                        </div>
                    </div>
                    <div class="stat-card-chart">
                        <div class="stat-card-label">Buku yang dipinjam</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card info">
                <div class="stat-card-content">
                    <div class="stat-card-header">
                        <div class="stat-card-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="stat-card-info">
                            <h3 class="stat-card-number"><?php echo $kembalikan_collection->countDocuments(); ?></h3>
                            <p class="stat-card-title">Dikembalikan</p>
                        </div>
                    </div>
                    <div class="stat-card-chart">
                        <div class="stat-card-label">Total buku dikembalikan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Activities -->
    <div class="row fade-in-delayed">
        <div class="col-md-6">
            <div class="glass-card">
                <div class="card-body">
                    <div class="section-header">
                        <h5><i class="fas fa-bolt"></i> Aksi Cepat</h5>
                        <span class="section-subtitle">Akses cepat ke fitur utama</span>
                    </div>
                    <div class="modern-grid">
                        <a href="buku/tambah_buku.php" class="grid-item">
                            <div class="item-icon" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="item-content">
                                <h6>Tambah Buku</h6>
                                <p>Tambah koleksi baru</p>
                            </div>
                            <div class="item-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="pinjam/tambah_pinjam.php" class="grid-item">
                            <div class="item-icon" style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);">
                                <i class="fas fa-hand-holding"></i>
                            </div>
                            <div class="item-content">
                                <h6>Pinjam Buku</h6>
                                <p>Catat peminjaman baru</p>
                            </div>
                            <div class="item-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="kembalikan/tambah_kembali.php" class="grid-item">
                            <div class="item-icon" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div class="item-content">
                                <h6>Kembalikan Buku</h6>
                                <p>Proses pengembalian</p>
                            </div>
                            <div class="item-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="buku/index.php" class="grid-item">
                            <div class="item-icon" style="background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="item-content">
                                <h6>Daftar Buku</h6>
                                <p>Lihat semua koleksi</p>
                            </div>
                            <div class="item-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card">
                <div class="card-body">
                    <div class="section-header">
                        <h5><i class="fas fa-history"></i> Aktivitas Terbaru</h5>
                        <span class="section-subtitle">Riwayat aktivitas terakhir</span>
                    </div>
                    <div class="modern-timeline">
                        <?php foreach ($recentPeminjaman as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="activity-icon borrow">
                                        <i class="fas fa-hand-holding"></i>
                                    </div>
                                    <div class="activity-details">
                                        <h6><?php echo htmlspecialchars($activity['nama_peminjam']); ?></h6>
                                        <p>meminjam buku "<?php echo htmlspecialchars($activity['judul_buku']); ?>"</p>
                                        <span class="activity-time">
                                            <i class="far fa-clock"></i>
                                            <?php echo $activity['tanggal_pinjam']->toDateTime()->format('d M Y H:i'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php foreach ($recentPengembalian as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <div class="activity-icon return">
                                        <i class="fas fa-undo"></i>
                                    </div>
                                    <div class="activity-details">
                                        <h6><?php echo htmlspecialchars($activity['nama_peminjam']); ?></h6>
                                        <p>mengembalikan buku "<?php echo htmlspecialchars($activity['judul_buku']); ?>"</p>
                                        <span class="activity-time">
                                            <i class="far fa-clock"></i>
                                            <?php echo $activity['tanggal_kembali']->toDateTime()->format('d M Y H:i'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
