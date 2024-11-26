<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* CSS untuk mengubah gaya navbar */
        .navbar-nav .nav-item .nav-link.active {
            font-weight: bold;
            color: #ffffff !important;
            background-color: grey;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">
            <img src="Logo.png" alt="Logo" style="width: 150px; height: 50px;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="kelola_produk.php">Kelola Produk</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_merek.php">Kelola Merek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="input_penjualan.php">Input Penjualan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_penjualan.php">History Penjualan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="laporan_bulanan.php">Laporan Bulanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="import_data.php">Impor Data</a>
                </li>
            </ul>
        </div>
    </nav>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            var path = window.location.pathname.split("/").pop();
            if (path == '') {
                path = 'index.php';
            }
            var target = $('nav a[href="' + path + '"]');
            target.addClass('active');
        });
    </script>
</body>

</html>
