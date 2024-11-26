<?php
include 'functions.php';
$merek = getMerek();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $merek_id = $_POST['merek_id'];
    $nama_produk = $_POST['nama_produk'];
    $ukuran = $_POST['ukuran'];
    $ring = $_POST['ring'];
    $modal = $_POST['modal'];
    $harga_jual = $_POST['harga_jual'];
    $jumlah_stok_awal = $_POST['jumlah_stok_awal'];
    $stok_minimum = $_POST['stok_minimum'];

    $sql = "INSERT INTO produk_ban (merek_id, nama_produk, ukuran, ring) VALUES ('$merek_id', '$nama_produk', '$ukuran', '$ring')";
    if ($conn->query($sql) === TRUE) {
        $produk_id = $conn->insert_id;
        $sql_harga = "INSERT INTO harga (produk_id, modal, harga_jual) VALUES ('$produk_id', '$modal', '$harga_jual')";
        $sql_stok = "INSERT INTO stok (produk_id, jumlah_stok_awal, jumlah_stok_sisa, stok_minimum) VALUES ('$produk_id', '$jumlah_stok_awal', '$jumlah_stok_awal', '$stok_minimum')";
        
        if ($conn->query($sql_harga) === TRUE && $conn->query($sql_stok) === TRUE) {
            echo "Produk baru berhasil ditambahkan!";
        } else {
            echo "Terjadi kesalahan dalam menambahkan harga atau stok produk.";
        }
    } else {
        echo "Terjadi kesalahan dalam menambahkan produk.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk Baru</title>
</head>
<body>
    <h1>Tambah Produk Baru</h1>
    <form method="POST" action="">
        <label for="merek_id">Pilih Merek:</label>
        <select id="merek_id" name="merek_id" required>
            <option value="">--Pilih Merek--</option>
            <?php while($row = $merek->fetch_assoc()) { ?>
            <option value="<?php echo $row['id']; ?>"><?php echo $row['nama_merek']; ?></option>
            <?php } ?>
        </select>
        <br><br>
        <label for="nama_produk">Nama Produk:</label>
        <input type="text" id="nama_produk" name="nama_produk" required>
        <br><br>
        <label for="ukuran">Ukuran:</label>
        <input type="text" id="ukuran" name="ukuran" required>
        <br><br>
        <label for="ring">Ring:</label>
        <input type="text" id="ring" name="ring" required>
        <br><br>
        <label for="modal">Modal:</label>
        <input type="number" id="modal" name="modal" required>
        <br><br>
        <label for="harga_jual">Harga Jual:</label>
        <input type="number" id="harga_jual" name="harga_jual" required>
        <br><br>
        <label for="jumlah_stok_awal">Jumlah Stok Awal:</label>
        <input type="number" id="jumlah_stok_awal" name="jumlah_stok_awal" required>
        <br><br>
        <label for="stok_minimum">Stok Minimum:</label>
        <input type="number" id="stok_minimum" name="stok_minimum" required>
        <br><br>
        <input type="submit" value="Tambah Produk">
    </form>
    <a href="index.php">Kembali ke Dashboard</a>
</body>
</html>
