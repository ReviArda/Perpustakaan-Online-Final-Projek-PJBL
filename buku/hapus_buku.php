<?php
session_start();
require '../config/connection.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit;
}

if (isset($_GET['id'])) {
    try {
        $id = new MongoDB\BSON\ObjectId($_GET['id']);
        
        // Cek apakah buku sedang dipinjam
        $is_borrowed = $peminjaman_collection->countDocuments([
            'kode_buku' => $id,
            'status' => 'dipinjam'
        ]);

        if ($is_borrowed > 0) {
            $_SESSION['error'] = "Buku tidak dapat dihapus karena sedang dipinjam.";
        } else {
            $result = $buku_collection->deleteOne(['_id' => $id]);
            
            if ($result->getDeletedCount() > 0) {
                $_SESSION['success'] = "Buku berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Buku tidak ditemukan.";
            }
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

header("Location: index.php");
exit; 