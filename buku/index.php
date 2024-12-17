<?php
session_start();
require '../config/connection.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

// Ambil semua data buku, urutkan berdasarkan tanggal pembuatan terbaru
$books = $buku_collection->find([], ['sort' => ['created_at' => -1]]);

// Hitung total buku yang dipinjam
$bukuDipinjam = $peminjaman_collection->countDocuments(['status' => 'dipinjam']);

// Hitung total buku yang tersedia (total buku - buku yang dipinjam)
$bukuTersedia = $buku_collection->countDocuments() - $bukuDipinjam;

// Hitung total kategori buku yang unik
$totalKategori = count(array_unique($buku_collection->distinct('kategori')));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku - Sistem Perpustakaan</title>
    <!-- Memasukkan Bootstrap dan FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container table-page mt-4">
        <div class="form-card">
            <div class="card-body">
                <!-- Header Section -->
                <div class="form-header">
                    <h2 class="form-title">
                        <i class="fas fa-book"></i>
                        Data Buku
                    </h2>
                    <p class="text-muted">Kelola data buku perpustakaan</p>
                </div>

                <!-- Statistik Buku -->
                <div class="stats-container mb-4">
                    <div class="row">
                        <!-- Total Buku -->
                        <div class="col-md-3">
                            <div class="mini-stat-card">
                                <div class="stat-icon blue">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $buku_collection->countDocuments(); ?></h3>
                                    <p>Total Buku</p>
                                </div>
                            </div>
                        </div>
                        <!-- Buku Tersedia -->
                        <div class="col-md-3">
                            <div class="mini-stat-card">
                                <div class="stat-icon green">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $bukuTersedia; ?></h3>
                                    <p>Buku Tersedia</p>
                                </div>
                            </div>
                        </div>
                        <!-- Buku Dipinjam -->
                        <div class="col-md-3">
                            <div class="mini-stat-card">
                                <div class="stat-icon orange">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $bukuDipinjam; ?></h3>
                                    <p>Buku Dipinjam</p>
                                </div>
                            </div>
                        </div>
                        <!-- Total Kategori -->
                        <div class="col-md-3">
                            <div class="mini-stat-card">
                                <div class="stat-icon purple">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo $totalKategori; ?></h3>
                                    <p>Kategori</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pencarian dan Filter -->
                <div class="search-filter-container mb-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="filter-buttons">
                                <!-- Input Pencarian -->
                                <div class="search-box">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Cari buku...">
                                    <i class="fas fa-search search-icon"></i>
                                </div>
                                <!-- Filter Kategori -->
                                <select class="form-select" id="kategoriFilter">
                                    <option value="">Semua Kategori</option>
                                    <option value="Novel">Novel</option>
                                    <option value="Pendidikan">Pendidikan</option>
                                    <option value="Komik">Komik</option>
                                </select>
                                <!-- Filter Urutan -->
                                <select class="form-select" id="sortBy">
                                    <option value="judul">Urutkan: Judul</option>
                                    <option value="terbaru">Terbaru</option>
                                    <option value="stok">Stok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data Buku -->
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th class="no-column">No</th>
                                <th class="kode-column">Kode Buku</th>
                                <th class="judul-column">Judul</th>
                                <th class="pengarang-column">Pengarang</th>
                                <th class="penerbit-column">Penerbit</th>
                                <th class="tahun-column">Tahun</th>
                                <th class="kategori-column">Kategori</th>
                                <th class="stok-column">Stok</th>
                                <th class="aksi-column">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($books as $book) {
                                echo "<tr>";
                                echo "<td>{$no}</td>";
                                echo "<td>{$book['kode_buku']}</td>";
                                echo "<td>{$book['judul']}</td>";
                                echo "<td>{$book['pengarang']}</td>";
                                echo "<td>{$book['penerbit']}</td>";
                                echo "<td>{$book['tahun_terbit']}</td>";
                                echo "<td>{$book['kategori']}</td>";
                                echo "<td>{$book['stok']}</td>";
                                echo "<td>
                                    <div class='action-buttons'>
                                        <!-- Edit Buku -->
                                        <a href='edit_buku.php?id={$book['_id']}' class='btn-action btn-warning'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <!-- Hapus Buku -->
                                        <a href='hapus_buku.php?id={$book['_id']}' class='btn-action btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\")'>
                                            <i class='fas fa-trash'></i>
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

                <!-- Tombol Kembali -->
                <div class="mt-4">
                    <a href="../main.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus item ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Loading state
    function showLoading() {
        document.querySelector('.loading-overlay').style.display = 'flex';
    }

    function hideLoading() {
        document.querySelector('.loading-overlay').style.display = 'none';
    }

    // Delete confirmation
    const deleteButtons = document.querySelectorAll('a[href*="hapus_buku.php"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
            
            document.getElementById('confirmDelete').onclick = function() {
                showLoading();
                window.location.href = href;
            };
        });
    });

    // Filter by kategori
    document.getElementById('kategoriFilter').addEventListener('change', function() {
        const kategori = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const kategoriCell = row.cells[6].textContent.toLowerCase();
            row.style.display = !kategori || kategoriCell === kategori ? '' : 'none';
        });
    });

    // Sort functionality
    document.getElementById('sortBy').addEventListener('change', function() {
        const sortBy = this.value;
        const tbody = document.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortBy) {
                case 'judul':
                    aValue = a.cells[2].textContent;
                    bValue = b.cells[2].textContent;
                    return aValue.localeCompare(bValue);
                case 'terbaru':
                    return -1; // Assuming newest is already at top
                case 'stok':
                    aValue = parseInt(a.cells[7].textContent);
                    bValue = parseInt(b.cells[7].textContent);
                    return bValue - aValue;
            }
        });
        
        rows.forEach(row => tbody.appendChild(row));
    });
    </script>
</body>
</html> 