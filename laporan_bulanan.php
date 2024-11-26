<?php
include 'functions.php';
include 'navbar.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$merek_id = isset($_GET['merek_id']) ? $_GET['merek_id'] : '';

$laporan_bulanan = getLaporanBulanan($bulan, $tahun);
$merek = getMerek();

if ($merek_id) {
    $laporan_per_merek = getLaporanKeuntunganByMerek($bulan, $tahun, $merek_id);
    $merek_nama = getNamaMerekById($merek_id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Laporan Bulanan</h1>
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="bulan">Bulan:</label>
                    <select id="bulan" name="bulan" class="form-control">
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($i == $bulan) echo 'selected'; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="tahun">Tahun:</label>
                    <select id="tahun" name="tahun" class="form-control">
                        <?php for ($i = date('Y') - 5; $i <= date('Y') + 5; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if ($i == $tahun) echo 'selected'; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="merek_id">Pilih Merek:</label>
                    <select id="merek_id" name="merek_id" class="form-control">
                        <option value="">--Pilih Merek--</option>
                        <?php while ($row = $merek->fetch_assoc()) { ?>
                            <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $merek_id) echo 'selected'; ?>>
                                <?php echo $row['nama_merek']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">Tampilkan Laporan</button>
                </div>
            </div>
        </form>

        <?php if ($merek_id) { ?>
            <h2>Penjualan untuk Merek: <?php echo $merek_nama; ?></h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Ukuran</th>
                            <th>Ring</th>
                            <th>Total Terjual</th>
                            <th>Modal per Unit</th>
                            <th>Harga Jual per Unit</th>
                            <th>Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $laporan_per_merek->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['nama_produk']; ?></td>
                                <td><?php echo isset($row['ukuran']) ? $row['ukuran'] : '-'; ?></td>
                                <td><?php echo isset($row['ring']) ? $row['ring'] : '-'; ?></td>
                                <td><?php echo isset($row['total_terjual']) ? $row['total_terjual'] : '0'; ?></td>
                                <td><?php echo isset($row['modal']) ? number_format($row['modal'], 2) : '0.00'; ?></td>
                                <td><?php echo isset($row['harga_jual']) ? number_format($row['harga_jual'], 2) : '0.00'; ?></td>
                                <td><?php echo isset($row['keuntungan']) ? number_format($row['keuntungan'], 2) : '0.00'; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <h2>Laporan Penjualan untuk Bulan <?php echo date('F', mktime(0, 0, 0, $bulan, 10)); ?> Tahun <?php echo $tahun; ?></h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Merek</th>
                            <th>Total Terjual</th>
                            <th>Keuntungan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $laporan_bulanan->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['nama_merek']; ?></td>
                                <td><?php echo $row['total_terjual']; ?></td>
                                <td><?php echo number_format($row['keuntungan'], 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
        <a href="index.php" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
