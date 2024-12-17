<?php
session_start();
require '../config/connection.php';
require '../config/helpers.php';

// Cek login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

$success_message = $error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $kode_buku = generateKodeBuku();
        
        $data = [
            'kode_buku' => $kode_buku,
            'judul' => $_POST['judul'],
            'pengarang' => $_POST['pengarang'],
            'penerbit' => $_POST['penerbit'],
            'tahun_terbit' => intval($_POST['tahun_terbit']),
            'kategori' => $_POST['kategori'],
            'stok' => intval($_POST['stok']),
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ];

        $result = $buku_collection->insertOne($data);
        
        if ($result->getInsertedCount() > 0) {
            $success_message = "Buku berhasil ditambahkan!";
        } else {
            $error_message = "Gagal menambahkan buku.";
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
    <title>Tambah Buku - Sistem Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container mt-4">
        <div class="form-card">
            <div class="card-body">
                <!-- Form Header -->
                <div class="form-header">
                    <h2 class="form-title">
                        <i class="fas fa-plus-circle"></i>
                        Tambah Buku Baru
                    </h2>
                    <p class="text-muted">Silahkan isi data buku yang akan ditambahkan</p>
                </div>

                <!-- Alert Messages -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Form Content -->
                <form method="POST" action="" class="animated-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" name="judul" required 
                                    placeholder="Masukkan judul buku">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Pengarang</label>
                                <input type="text" class="form-control" name="pengarang" required 
                                    placeholder="Masukkan nama pengarang">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Penerbit</label>
                                <input type="text" class="form-control" name="penerbit" required 
                                    placeholder="Masukkan nama penerbit">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tahun Terbit</label>
                                <input type="number" class="form-control" name="tahun_terbit" required 
                                    placeholder="Masukkan tahun terbit" min="1900" max="<?php echo date('Y'); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Novel">Novel</option>
                                    <option value="Pendidikan">Pendidikan</option>
                                    <option value="Komik">Komik</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" required 
                                    placeholder="Masukkan jumlah stok" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                    <a href="../main.php" class="btn btn-secondary">  <!-- Ubah dari main.php ke index.php -->
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 