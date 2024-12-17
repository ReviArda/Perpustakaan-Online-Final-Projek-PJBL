<?php
require __DIR__ . '/../vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    
    // Database admin_book
    $database = $client->admin_book;
    
    // Collections
    $buku_collection = $database->buku;
    $peminjaman_collection = $database->peminjaman;
    $kembalikan_collection = $database->kembalikan_buku;
    
} catch (Exception $e) {
    die("Error: Couldn't connect to the database. " . $e->getMessage());
} 