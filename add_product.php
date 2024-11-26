<?php
include 'functions.php';
$merek_ban = getMerek();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $merek_id = $_POST['merek_id'];
    $ukuran = $_POST['ukuran'];
    $ring = $_POST['ring'];
    $modal = $_POST['modal'];
    $harga_jual = $_POST['harga_jual'];
    $jumlah_stok_awal = $_POST['jumlah_stok_awal'];
    $stok_minimum = $_POST['stok_minimum'];

    $result = addProduk($nama_produk, $merek_id, $ukuran, $ring, $modal, $harga_jual, $jumlah_stok_awal, $stok_minimum);

    if ($result) {
        $message = "Produk baru berhasil ditambahkan!";
    } else {
        $message = "Terjadi kesalahan dalam menambahkan produk.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Omah Ban</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Omah Ban</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_merek.php">Kelola Merek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="input_penjualan.php">Input Penjualan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_penjualan.php">Lihat Penjualan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_keuntungan.php">Lihat Keuntungan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="laporan_bulanan.php">Laporan Bulanan</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="add_product.php">Tambah Produk Baru</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="import_data.php">Impor Data</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Tambah Produk Baru</h1>
        <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nama_produk">Nama Produk:</label>
                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
            </div>
            <div class="form-group">
                <label for="merek_id">Merek:</label>
                <select class="form-control" id="merek_id" name="merek_id" required>
                    <option value="">Pilih Merek</option>
                    <?php while($merek = $merek_ban->fetch_assoc()) { ?>
                        <option value="<?php echo $merek['id']; ?>"><?php echo $merek['nama_merek']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ukuran">Ukuran:</label>
                <input type="text" class="form-control" id="ukuran" name="ukuran" required>
            </div>
            <div class="form-group">
                <label for="ring">Ring:</label>
                <input type="text" class="form-control" id="ring" name="ring" required>
            </div>
            <div class="form-group">
                <label for="modal">Modal:</label>
                <input type="number" step="0.01" class="form-control" id="modal" name="modal" required>
            </div>
            <div class="form-group">
                <label for="harga_jual">Harga Jual:</label>
                <input type="number" step="0.01" class="form-control" id="harga_jual" name="harga_jual" required>
            </div>
            <div class="form-group">
                <label for="jumlah_stok_awal">Jumlah Stok Awal:</label>
                <input type="number" class="form-control" id="jumlah_stok_awal" name="jumlah_stok_awal" required>
            </div>
            <div class="form-group">
                <label for="stok_minimum">Stok Minimum:</label>
                <input type="number" class="form-control" id="stok_minimum" name="stok_minimum" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Produk</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
