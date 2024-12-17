<?php
require 'connection.php';

try {
    // Buat indexes untuk performa
    $buku_collection->createIndex(['kode_buku' => 1], ['unique' => true]);
    $peminjaman_collection->createIndex(['kode_pinjam' => 1], ['unique' => true]);
    $peminjaman_collection->createIndex(['kode_buku' => 1]);
    $kembalikan_collection->createIndex(['kode_kembali' => 1], ['unique' => true]);
    $kembalikan_collection->createIndex(['kode_pinjam' => 1]);
    
    echo "Database dan collections berhasil dibuat!";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
} 