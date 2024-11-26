<?php
include 'functions.php';

if (isset($_GET['id'])) {
    $produk_id = $_GET['id'];

    // Hapus data dari tabel harga
    $sql_harga = "DELETE FROM harga WHERE produk_id = $produk_id";
    $conn->query($sql_harga);

    // Hapus data dari tabel stok
    $sql_stok = "DELETE FROM stok WHERE produk_id = $produk_id";
    $conn->query($sql_stok);

    // Hapus data dari tabel produk_ban
    $sql_produk = "DELETE FROM produk_ban WHERE id = $produk_id";
    if ($conn->query($sql_produk) === TRUE) {
        echo "Produk berhasil dihapus!";
    } else {
        echo "Terjadi kesalahan dalam menghapus produk.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hapus Produk</title>
</head>
<body>
    <a href="view_stok.php">Kembali ke Daftar Stok</a>
</body>
</html>
