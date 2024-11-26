<?php
include 'functions.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$stok = searchProduk($search);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stok Barang</title>
    <style>
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            margin: 0 5px;
        }
        .btn-edit {
            background-color: #4CAF50;
        }
        .btn-delete {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <h1>Stok Barang</h1>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Cari produk..." value="<?php echo $search; ?>">
        <input type="submit" value="Cari">
    </form>
    <br>
    <table border="1">
        <tr>
            <th>Nama Produk</th>
            <th>Stok Awal</th>
            <th>Stok Sisa</th>
            <th>Stok Minimum</th>
            <th>Opsi</th>
        </tr>
        <?php while($row = $stok->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['nama_produk']; ?></td>
            <td><?php echo $row['jumlah_stok_awal']; ?></td>
            <td><?php echo $row['jumlah_stok_sisa']; ?></td>
            <td><?php echo $row['stok_minimum']; ?></td>
            <td>
                <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <a href="index.php">Kembali ke Dashboard</a>
</body>
</html>
