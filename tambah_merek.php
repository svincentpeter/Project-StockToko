<?php
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_merek = $_POST['nama_merek'];

    if (addMerek($nama_merek)) {
        echo "Merek baru berhasil ditambahkan!";
    } else {
        echo "Terjadi kesalahan dalam menambahkan merek.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Merek Baru</title>
</head>
<body>
    <h1>Tambah Merek Baru</h1>
    <form method="POST" action="">
        <label for="nama_merek">Nama Merek:</label>
        <input type="text" id="nama_merek" name="nama_merek" required>
        <br><br>
        <input type="submit" value="Tambah Merek">
    </form>
    <a href="index.php">Kembali ke Dashboard</a>
</body>
</html>
