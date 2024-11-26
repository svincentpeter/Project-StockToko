<?php
include 'functions.php';
include 'navbar.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $filePath = $_FILES['file']['tmp_name'];
    $errors = importDataFromExcel($filePath);
    if (empty($errors)) {
        $message = "Data berhasil diimpor!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impor Data dari Excel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Impor Data dari Excel</h1>
        <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>
        <?php if (!empty($errors)) { ?>
            <div class='alert alert-danger'>
                <h4>Kesalahan Data:</h4>
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li>Baris <?php echo $error['row']; ?>: <?php echo implode(', ', $error['errors']); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="file" class="form-label">Pilih File Excel:</label>
                <input type="file" class="form-control" id="file" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary">Impor Data</button>
        </form>
        <div class="mt-4">
            <h4>Syarat Import Data</h4>
            <p>Pastikan file Excel Anda sesuai dengan format berikut:</p>
            <ul>
                <li>Kolom 1: Nama Produk (Wajib diisi, teks)</li>
                <li>Kolom 2: Merek ID (Wajib diisi, angka positif)</li>
                <li>Kolom 3: Ukuran (Wajib diisi, teks)</li>
                <li>Kolom 4: Ring (Wajib diisi, angka positif)</li>
                <li>Kolom 5: Modal per Unit (Wajib diisi, angka positif atau nol)</li>
                <li>Kolom 6: Harga Jual per Unit (Wajib diisi, angka positif atau nol)</li>
                <li>Kolom 7: Jumlah Stok Awal (Wajib diisi, angka positif atau nol)</li>
                <li>Kolom 8: Jumlah Stok Sisa (Wajib diisi, angka positif atau nol)</li>
                <li>Kolom 9: Stok Minimum (Wajib diisi, angka positif atau nol)</li>
            </ul>
            <p>Contoh template Excel:</p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Merek ID</th>
                        <th>Ukuran</th>
                        <th>Ring</th>
                        <th>Modal per Unit</th>
                        <th>Harga Jual per Unit</th>
                        <th>Jumlah Stok Awal</th>
                        <th>Jumlah Stok Sisa</th>
                        <th>Stok Minimum</th>
                    </tr>
                    </thead>
                <tbody>
                    <tr>
                        <td>Contoh Produk</td>
                        <td>1</td>
                        <td>175</td>
                        <td>13</td>
                        <td>450000</td>
                        <td>750000</td>
                        <td>10</td>
                        <td>10</td>
                        <td>2</td>
                    </tr>
                    <!-- Tambahkan contoh baris lain jika perlu -->
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
