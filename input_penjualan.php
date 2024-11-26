<?php
include 'functions.php';
include 'navbar.php';

$merek = getMerek();
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produk_id = $_POST['produk_id'];
    $tanggal = $_POST['tanggal'];
    $jumlah_terjual = $_POST['jumlah_terjual'];
    if (inputPenjualan($produk_id, $tanggal, $jumlah_terjual)) {
        $message = "Penjualan berhasil diinput!";
    } else {
        $message = "Terjadi kesalahan dalam menginput penjualan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Penjualan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Input Penjualan Harian</h1>

        <?php if ($message) { ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <script>
            setTimeout(function() {
                $('.alert').alert('close');
            }, 2000);
        </script>
        <?php } ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="merek_id">Pilih Merek:</label>
                <select class="form-control" id="merek_id" name="merek_id" onchange="getProdukByMerek(this.value)" required>
                    <option value="">--Pilih Merek--</option>
                    <?php while($row = $merek->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nama_merek']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="produk_id">Pilih Produk:</label>
                <select class="form-control" id="produk_id" name="produk_id" required>
                    <option value="">--Pilih Produk--</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="jumlah_terjual">Jumlah Terjual:</label>
                <input type="number" class="form-control" id="jumlah_terjual" name="jumlah_terjual" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script>
        function getProdukByMerek(merek_id) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_produk.php?merek_id=" + merek_id, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("produk_id").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
