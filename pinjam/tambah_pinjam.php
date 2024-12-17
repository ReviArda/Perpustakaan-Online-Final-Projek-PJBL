<?php
session_start();
require '../config/connection.php';
require '../config/helpers.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

$success_message = $error_message = "";

// Ambil daftar buku yang tersedia
$available_books = $buku_collection->find(['stok' => ['$gt' => 0]]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $kode_pinjam = generateKodePinjam();
        $kode_buku = $_POST['kode_buku'];
        
        // Ambil informasi buku
        $buku = $buku_collection->findOne(['kode_buku' => $kode_buku]);
        
        if ($buku && $buku['stok'] > 0) {
            // Kurangi stok buku
            $buku_collection->updateOne(
                ['kode_buku' => $kode_buku],
                ['$inc' => ['stok' => -1]]
            );
            
            // Tambah data peminjaman
            $data = [
                'kode_pinjam' => $kode_pinjam,
                'kode_buku' => $kode_buku,
                'judul_buku' => $buku['judul'],
                'nama_peminjam' => $_POST['nama_peminjam'],
                'tanggal_pinjam' => new MongoDB\BSON\UTCDateTime(),
                'tanggal_kembali' => new MongoDB\BSON\UTCDateTime(strtotime("+7 days") * 1000),
                'status' => 'dipinjam',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ];

            $result = $peminjaman_collection->insertOne($data);
            
            if ($result->getInsertedCount() > 0) {
                $success_message = "Peminjaman berhasil dicatat!";
            } else {
                $error_message = "Gagal mencatat peminjaman.";
            }
        } else {
            $error_message = "Buku tidak tersedia.";
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
    <title>Tambah Peminjaman - Sistem Perpustakaan</title>
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
                        Tambah Peminjaman
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
                        <label class="form-label">Pilih Buku</label>
                        <select class="form-select" name="kode_buku" required>
                            <option value="">Pilih Buku</option>
                            <?php foreach ($available_books as $book): ?>
                                <option value="<?php echo $book['kode_buku']; ?>">
                                    <?php echo $book['judul']; ?> (Stok: <?php echo $book['stok']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Peminjam</label>
                        <input type="text" class="form-control" name="nama_peminjam" required>
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