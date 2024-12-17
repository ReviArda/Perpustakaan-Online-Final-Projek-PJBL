<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);
    $loan = $peminjaman_collection->findOne(['_id' => $id]);
    
    if (!$loan) {
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peminjaman - Sistem Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container mt-4 crud-content">
        <div class="card form-card">
            <div class="card-body">
                <div class="page-header">
                    <h2 class="page-title">
                        <i class="fas fa-info-circle"></i>
                        Detail Peminjaman
                    </h2>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <table class="detail-table table">
                            <tr>
                                <th>Kode Peminjaman</th>
                                <td><?php echo $loan['kode_pinjam']; ?></td>
                            </tr>
                            <tr>
                                <th>Judul Buku</th>
                                <td><?php echo $loan['judul_buku']; ?></td>
                            </tr>
                            <tr>
                                <th>Nama Peminjam</th>
                                <td><?php echo $loan['nama_peminjam']; ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Pinjam</th>
                                <td><?php echo $loan['tanggal_pinjam']->toDateTime()->format('d/m/Y H:i'); ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal Kembali</th>
                                <td><?php echo $loan['tanggal_kembali']->toDateTime()->format('d/m/Y H:i'); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge <?php echo $loan['status'] === 'dipinjam' ? 'bg-warning' : 'bg-success'; ?>">
                                        <?php echo $loan['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <a href="index.php" class="btn btn-secondary mt-4">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 