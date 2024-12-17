<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

$success_message = $error_message = "";

// Ambil data buku yang akan diedit
if (isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);
    $book = $buku_collection->findOne(['_id' => $id]);
    
    if (!$book) {
        header("Location: index.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $result = $buku_collection->updateOne(
            ['_id' => $id],
            ['$set' => [
                'judul' => $_POST['judul'],
                'pengarang' => $_POST['pengarang'],
                'penerbit' => $_POST['penerbit'],
                'tahun_terbit' => intval($_POST['tahun_terbit']),
                'kategori' => $_POST['kategori'],
                'stok' => intval($_POST['stok']),
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]]
        );
        
        if ($result->getModifiedCount() > 0) {
            $success_message = "Buku berhasil diupdate!";
        } else {
            $error_message = "Tidak ada perubahan data.";
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
    <title>Edit Buku - Sistem Perpustakaan</title>
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
                        <i class="fas fa-edit"></i>
                        Edit Buku
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
                        <label class="form-label">Judul Buku</label>
                        <input type="text" class="form-control" name="judul" value="<?php echo $book['judul']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" class="form-control" name="pengarang" value="<?php echo $book['pengarang']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Penerbit</label>
                        <input type="text" class="form-control" name="penerbit" value="<?php echo $book['penerbit']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tahun Terbit</label>
                        <input type="number" class="form-control" name="tahun_terbit" value="<?php echo $book['tahun_terbit']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Novel" <?php echo $book['kategori'] == 'Novel' ? 'selected' : ''; ?>>Novel</option>
                            <option value="Pendidikan" <?php echo $book['kategori'] == 'Pendidikan' ? 'selected' : ''; ?>>Pendidikan</option>
                            <option value="Komik" <?php echo $book['kategori'] == 'Komik' ? 'selected' : ''; ?>>Komik</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" class="form-control" name="stok" value="<?php echo $book['stok']; ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="index.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 