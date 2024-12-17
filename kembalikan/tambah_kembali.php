<?php
session_start();
require '../config/connection.php';
require '../config/helpers.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

$success_message = $error_message = "";

// Ambil daftar peminjaman yang belum dikembalikan
$active_loans = $peminjaman_collection->find(['status' => 'dipinjam']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validasi input
        if (!isset($_POST['kode_pinjam']) || empty($_POST['kode_pinjam'])) {
            throw new Exception("Pilih data peminjaman terlebih dahulu!");
        }

        // Ambil data peminjaman
        $peminjaman = $peminjaman_collection->findOne(['kode_pinjam' => $_POST['kode_pinjam']]);
        
        if (!$peminjaman) {
            throw new Exception("Data peminjaman tidak ditemukan!");
        }

        // Tanggal kembali seharusnya dari data peminjaman
        $tanggal_seharusnya = $peminjaman['tanggal_kembali']->toDateTime();
        // Tanggal kembali aktual (hari ini)
        $tanggal_aktual = new DateTime();
        
        // Hitung denda
        $denda = hitungDenda($tanggal_seharusnya, $tanggal_aktual);
        
        // Data pengembalian
        $data = [
            'kode_kembali' => generateKodeKembali(),
            'kode_pinjam' => $peminjaman['kode_pinjam'],
            'judul_buku' => $peminjaman['judul_buku'],
            'nama_peminjam' => $peminjaman['nama_peminjam'],
            'tanggal_pinjam' => $peminjaman['tanggal_pinjam'],
            'tanggal_kembali' => new MongoDB\BSON\UTCDateTime(),
            'denda' => $denda,
            'keterangan' => $denda > 0 ? 'Terlambat ' . $tanggal_aktual->diff($tanggal_seharusnya)->days . ' hari' : 'Tepat waktu',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];
        
        // Simpan data pengembalian
        $result = $kembalikan_collection->insertOne($data);
        
        if ($result->getInsertedCount() > 0) {
            // Update status peminjaman
            $peminjaman_collection->updateOne(
                ['kode_pinjam' => $_POST['kode_pinjam']],
                ['$set' => ['status' => 'dikembalikan']]
            );
            
            // Update stok buku
            $buku_collection->updateOne(
                ['kode_buku' => $peminjaman['kode_buku']],
                ['$inc' => ['stok' => 1]]
            );
            
            $success_message = "Buku berhasil dikembalikan!";
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengembalian - Sistem Perpustakaan</title>
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
                        <i class="fas fa-plus-circle"></i>
                        Tambah Pengembalian
                    </h2>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST" action="" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label">Pilih Peminjaman</label>
                        <select class="form-select" name="kode_pinjam" required>
                            <option value="">Pilih Peminjaman</option>
                            <?php foreach ($active_loans as $loan): ?>
                                <option value="<?php echo $loan['kode_pinjam']; ?>">
                                    <?php echo $loan['judul_buku']; ?> - 
                                    <?php echo $loan['nama_peminjam']; ?> 
                                    (Pinjam: <?php echo $loan['tanggal_pinjam']->toDateTime()->format('d/m/Y'); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="../main.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 