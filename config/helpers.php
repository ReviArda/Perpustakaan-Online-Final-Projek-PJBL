<?php
function generateKodeBuku() {
    // Ambil kode buku terakhir dari database
    global $buku_collection;
    $lastBook = $buku_collection->findOne(
        [],
        ['sort' => ['kode_buku' => -1]]
    );
    
    if ($lastBook) {
        // Jika ada buku, ambil nomor terakhir dan tambahkan 1
        $lastNumber = intval(substr($lastBook['kode_buku'], 2));
        $newNumber = $lastNumber + 1;
    } else {
        // Jika belum ada buku, mulai dari 1
        $newNumber = 1;
    }
    
    // Format nomor dengan padding 3 digit
    return 'BK' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function generateKodePinjam() {
    // Ambil kode peminjaman terakhir dari database
    global $peminjaman_collection;
    $lastLoan = $peminjaman_collection->findOne(
        [],
        ['sort' => ['kode_pinjam' => -1]]
    );
    
    if ($lastLoan) {
        // Jika ada peminjaman, ambil nomor terakhir dan tambahkan 1
        $lastNumber = intval(substr($lastLoan['kode_pinjam'], 2));
        $newNumber = $lastNumber + 1;
    } else {
        // Jika belum ada peminjaman, mulai dari 1
        $newNumber = 1;
    }
    
    // Format nomor dengan padding 3 digit
    return 'PJ' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function generateKodeKembali() {
    // Ambil kode pengembalian terakhir dari database
    global $kembalikan_collection;
    $lastReturn = $kembalikan_collection->findOne(
        [],
        ['sort' => ['kode_kembali' => -1]]
    );
    
    if ($lastReturn) {
        // Jika ada pengembalian, ambil nomor terakhir dan tambahkan 1
        $lastNumber = intval(substr($lastReturn['kode_kembali'], 2));
        $newNumber = $lastNumber + 1;
    } else {
        // Jika belum ada pengembalian, mulai dari 1
        $newNumber = 1;
    }
    
    // Format nomor dengan padding 3 digit
    return 'KB' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

function hitungDenda($tanggal_kembali_seharusnya, $tanggal_kembali_aktual) {
    // Konversi ke DateTime objects jika belum
    if (!($tanggal_kembali_seharusnya instanceof DateTime)) {
        $tanggal_kembali_seharusnya = new DateTime($tanggal_kembali_seharusnya);
    }
    if (!($tanggal_kembali_aktual instanceof DateTime)) {
        $tanggal_kembali_aktual = new DateTime($tanggal_kembali_aktual);
    }
    
    // Hitung selisih hari
    $selisih = $tanggal_kembali_aktual->diff($tanggal_kembali_seharusnya)->days;
    
    // Jika tanggal kembali aktual lebih besar dari seharusnya
    if ($tanggal_kembali_aktual > $tanggal_kembali_seharusnya) {
        // Denda per hari = Rp 1.000
        $denda = $selisih * 1000;
        return $denda;
    }
    
    return 0; // Tidak ada denda
} 