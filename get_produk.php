<?php
include 'functions.php';

if (isset($_GET['merek_id'])) {
    $merek_id = $_GET['merek_id'];
    $produk = getProdukByMerek($merek_id);
    echo '<option value="">--Pilih Produk--</option>';
    while ($row = $produk->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">' . $row['nama_produk'] . ' - ' . $row['ukuran'] . ' - ' . $row['ring'] . '</option>';
    }
}
?>
