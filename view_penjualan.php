<?php
include 'functions.php';
include 'navbar.php';

$merek = getMerek();
$penjualan = [];

if (isset($_POST['merek_id'])) {
    $merek_id = $_POST['merek_id'];
    $penjualan = getPenjualanByMerek($merek_id);
} else {
    $penjualan = getPenjualan();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Omah Ban</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Laporan Penjualan</h1>

        <div class="form-group">
            <label for="merek_id">Pilih Merek:</label>
            <form method="POST" action="view_penjualan.php">
                <select class="form-control" id="merek_id" name="merek_id" onchange="this.form.submit()">
                    <option value="">Pilih Merek</option>
                    <?php while ($row = $merek->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>" <?php if (isset($_POST['merek_id']) && $_POST['merek_id'] == $row['id']) echo 'selected'; ?>>
                            <?php echo $row['nama_merek']; ?>
                        </option>
                    <?php } ?>
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Tanggal</th>
                        <th>Jumlah Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $penjualan->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <?php 
                            echo $row['nama_produk'];
                            if (isset($row['ukuran'])) echo ' - ' . $row['ukuran'];
                            if (isset($row['ring'])) echo ' - ' . $row['ring']; 
                            ?>
                        </td>
                        <td><?php echo $row['tanggal']; ?></td>
                        <td><?php echo $row['jumlah_terjual']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
