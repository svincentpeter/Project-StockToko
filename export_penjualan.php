<?php
include 'functions.php';

$data = getPenjualanData();
$exportData = [['Nama Produk', 'Tanggal', 'Jumlah Terjual']];

foreach ($data as $row) {
    $exportData[] = [$row['nama_produk'], $row['tanggal'], $row['jumlah_terjual']];
}

exportDataToExcel($exportData, 'laporan_penjualan.xlsx');

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="laporan_penjualan.xlsx"');
header('Cache-Control: max-age=0');
readfile('laporan_penjualan.xlsx');
exit;
?>
