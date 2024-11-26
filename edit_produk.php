<?php
include 'functions.php';

if (isset($_GET['id'])) {
    $produk_id = $_GET['id'];
    $sql = "SELECT pb.*, h.modal, h.harga_jual, s.jumlah_stok_awal, s.jumlah_stok_sisa, s.stok_minimum 
            FROM produk_ban pb 
            JOIN harga h ON pb.id = h.produk_id 
            JOIN stok s ON pb.id = s.produk_id 
            WHERE pb.id = $produk_id";
    $result = $conn->query($sql);
    $produk = $result->fetch_assoc();
    $merek = getMerek();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produk_id = $_POST['produk_id'];
    $merek_id = $_POST['merek_id'];
    $nama_produk = $_POST['nama_produk'];
    $ukuran = $_POST['ukuran'];
    $ring = $_POST['ring'];
    $modal = $_POST['modal'];
    $harga_jual = $_POST['harga_jual'];
    $jumlah_stok_awal = $_POST['jumlah_stok_awal'];
    $stok_minimum = $_POST['stok_minimum'];

    $sql_produk = "UPDATE produk_ban SET merek_id = '$merek_id', nama_produk = '$nama_produk', ukuran = '$ukuran', ring = '$ring' WHERE id = $produk_id";
    $sql_harga = "UPDATE harga SET modal = '$modal', harga_jual = '$harga_jual' WHERE produk_id = $produk_id";
    $sql_stok = "UPDATE stok SET jumlah_stok_awal = '$jumlah_stok_awal', jumlah_stok_sisa = '$jumlah_stok_awal', stok_minimum = '$stok_minimum' WHERE produk_id = $produk_id";

    if ($conn->query($sql_produk) === TRUE && $conn->query($sql_harga) === TRUE && $conn->query($sql_stok) === TRUE) {
        echo "Produk berhasil diperbarui!";
    } else {
        echo "Terjadi kesalahan dalam memperbarui produk.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
</head>
<body>
    <h1>Edit Produk</h1>
    <form method="POST" action="">
        <input type="hidden" name="produk_id" value="<?php echo $produk['id']; ?>">
        <label for="merek_id">Pilih Merek:</label>
        <select id="merek_id" name="merek_id" required>
            <?php while($row = $merek->fetch_assoc()) { ?>
            <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $produk['merek_id']) echo 'selected'; ?>>
                <?php echo $row['nama_merek']; ?>
            </option>
            <?php } ?>
        </select>
        <br><br>
        <label for="nama_produk">Nama Produk:</label>
        <input type="text" id="nama_produk" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required>
        <br><br>
        <label for="ukuran">Ukuran:</label>
        <input type="text" id="ukuran" name="ukuran" value="<?php echo $produk['ukuran']; ?>" required>
        <br><br>
        <label for="ring">Ring:</label>
        <input type="text" id="ring" name="ring" value="<?php echo $produk['ring']; ?>" required>
        <br><br>
        <label for="modal">Modal:</label>
        <input type="number" id="modal" name="modal" value="<?php echo $produk['modal']; ?>" required>
        <br><br>
        <label for="harga_jual">Harga Jual:</label>
        <input type="number" id="harga_jual" name="harga_jual" value="<?php echo $produk['harga_jual']; ?>" required>
        <br><br>
        <label for="jumlah_stok_awal">Jumlah Stok Awal:</label>
        <input type="number" id="jumlah_stok_awal" name="jumlah_stok_awal" value="<?php echo $produk['jumlah_stok_awal']; ?>" required>
        <br><br>
        <label for="stok_minimum">Stok Minimum:</label>
        <input type="number" id="stok_minimum" name="stok_minimum" value="<?php echo $produk['stok_minimum']; ?>" required>
        <br><br>
        <input type="submit" value="Perbarui Produk">
    </form>
    <a href="index.php">Kembali ke Dashboard</a>
</body>
</html>
