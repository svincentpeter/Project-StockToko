<?php
include 'functions.php';
include 'navbar.php';

$merek_ban = getMerek();
$stok_by_merek = [];

while ($merek = $merek_ban->fetch_assoc()) {
    $merek_id = $merek['id'];
    $stok_by_merek[$merek['nama_merek']] = getStokByMerek($merek_id)->fetch_all(MYSQLI_ASSOC);
}

$low_stock_products = getLowStockProducts();
$penjualan = getTotalPenjualanPerBulan(date('m'), date('Y'));
$produk = [];
$terjual = [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Omah Ban</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-custom {
            margin-bottom: 20px;
        }

        .table-responsive-custom {
            overflow-x: auto;
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 40vh;
            width: 80vw;
        }

        .dropdown-menu.show {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container mt-4">

        <h2 class="mt-5">Stok Barang</h2>
        <div class="form-group">
            <label for="selectMerek">Pilih Merek:</label>
            <select class="form-control" id="selectMerek" onchange="filterStok()">
                <option value="">Pilih Merek</option>
                <?php foreach ($stok_by_merek as $merek => $stok_produk) { ?>
                    <option value="<?php echo $merek; ?>"><?php echo $merek; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="table-responsive-custom">
            <table class="table table-bordered" id="stokTable">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Stok Awal</th>
                        <th>Stok Sisa</th>
                        <th>Stok Minimum</th>
                    </tr>
                </thead>
                <tbody id="stokBody">
                    <!-- Data akan diisi menggunakan JavaScript -->
                </tbody>
            </table>
        </div>

        <h2 class="mt-5">Produk Stok Rendah</h2>
        <div class="table-responsive-custom">
        <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Stok Sisa</th>
                        <th>Stok Minimum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $low_stock_products->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['nama_produk']; ?></td>
                        <td><?php echo $row['jumlah_stok_sisa']; ?></td>
                        <td><?php echo $row['stok_minimum']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        var stokByMerek = <?php echo json_encode($stok_by_merek); ?>;

        function filterStok() {
            var merek = document.getElementById('selectMerek').value;
            var stokBody = document.getElementById('stokBody');
            stokBody.innerHTML = '';

            if (merek && stokByMerek[merek]) {
                stokByMerek[merek].forEach(function(row) {
                    var tr = document.createElement('tr');

                    var namaProdukTd = document.createElement('td');
                    namaProdukTd.innerText = row.nama_produk;
                    tr.appendChild(namaProdukTd);

                    var stokAwalTd = document.createElement('td');
                    stokAwalTd.innerText = row.jumlah_stok_awal ? row.jumlah_stok_awal : 'Tidak ada data';
                    tr.appendChild(stokAwalTd);

                    var stokSisaTd = document.createElement('td');
                    stokSisaTd.innerText = row.jumlah_stok_sisa ? row.jumlah_stok_sisa : 'Tidak ada data';
                    tr.appendChild(stokSisaTd);

                    var stokMinimumTd = document.createElement('td');
                    stokMinimumTd.innerText = row.stok_minimum ? row.stok_minimum : 'Tidak ada data';
                    tr.appendChild(stokMinimumTd);

                    stokBody.appendChild(tr);
                });
            }
        }
    </script>
</body>
</html>
