<?php
include 'dbconfig.php';
require 'vendor/autoload.php'; // Include Composer's autoload file

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

function getMerek() {
    global $conn;
    $sql = "SELECT * FROM merek_ban";
    $result = $conn->query($sql);
    return $result;
}

function addMerek($nama_merek) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO merek_ban (nama_merek) VALUES (?)");
    $stmt->bind_param("s", $nama_merek);
    $result = $stmt->execute();
    if ($result) {
        addAuditLog('add', 'merek_ban', $conn->insert_id);
    }
    return $result;
}

function updateMerek($merek_id, $nama_merek) {
    global $conn;
    $stmt = $conn->prepare("UPDATE merek_ban SET nama_merek = ? WHERE id = ?");
    $stmt->bind_param("si", $nama_merek, $merek_id);
    $result = $stmt->execute();
    if ($result) {
        addAuditLog('update', 'merek_ban', $merek_id);
    }
    return $result;
}

function getProdukByMerek($merek_id) {
    global $conn;
    $sql = "SELECT pb.id, pb.nama_produk, pb.ukuran, pb.ring, s.jumlah_stok_awal, s.jumlah_stok_sisa, s.stok_minimum
            FROM produk_ban pb
            JOIN stok s ON pb.id = s.produk_id
            WHERE pb.merek_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getMerekNameById($merek_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT nama_merek FROM merek_ban WHERE id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['nama_merek'];
    }
    return '';
}

function getStok() {
    global $conn;
    $sql = "SELECT pb.nama_produk, s.jumlah_stok_awal, s.jumlah_stok_sisa, s.stok_minimum, pb.id
            FROM stok s
            JOIN produk_ban pb ON s.produk_id = pb.id";
    $result = $conn->query($sql);
    return $result;
}

function getPenjualan() {
    global $conn;
    $sql = "SELECT p.nama_produk, j.tanggal, j.jumlah_terjual
            FROM penjualan j
            JOIN produk_ban p ON j.produk_id = p.id
            ORDER BY j.tanggal DESC";
    $result = $conn->query($sql);
    return $result;
}

function inputPenjualan($produk_id, $tanggal, $jumlah_terjual) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO penjualan (produk_id, tanggal, jumlah_terjual)
                            VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $produk_id, $tanggal, $jumlah_terjual);
    $result = $stmt->execute();
    if ($result) {
        updateStok($produk_id, $jumlah_terjual);
        addAuditLog('add', 'penjualan', $conn->insert_id);
    }
    return $result;
}

function updateStok($produk_id, $jumlah_terjual) {
    global $conn;
    $stmt = $conn->prepare("UPDATE stok SET jumlah_stok_sisa = jumlah_stok_sisa - ? WHERE produk_id = ?");
    $stmt->bind_param("ii", $jumlah_terjual, $produk_id);
    $stmt->execute();
}

function getTotalPenjualanPerBulan($bulan, $tahun) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.nama_produk, SUM(j.jumlah_terjual) as total_terjual
                            FROM penjualan j
                            JOIN produk_ban p ON j.produk_id = p.id
                            WHERE MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
                            GROUP BY j.produk_id");
    $stmt->bind_param("ii", $bulan, $tahun);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getLowStockProducts() {
    global $conn;
    $sql = "SELECT pb.nama_produk, s.jumlah_stok_sisa, s.stok_minimum
            FROM stok s
            JOIN produk_ban pb ON s.produk_id = pb.id
            WHERE s.jumlah_stok_sisa <= s.stok_minimum";
    $result = $conn->query($sql);
    return $result;
}

function getLaporanKeuntungan($bulan, $tahun) {
    global $conn;
    $stmt = $conn->prepare("SELECT pb.nama_produk, SUM(j.jumlah_terjual) as total_terjual, h.modal, h.harga_jual,
                                   (SUM(j.jumlah_terjual) * (h.harga_jual - h.modal)) as keuntungan
                            FROM penjualan j
                            JOIN produk_ban pb ON j.produk_id = pb.id
                            JOIN harga h ON j.produk_id = h.produk_id
                            WHERE MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
                            GROUP BY j.produk_id");
    $stmt->bind_param("ii", $bulan, $tahun);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getLaporanBulanan($bulan, $tahun) {
    global $conn;
    $stmt = $conn->prepare("SELECT mb.nama_merek, SUM(j.jumlah_terjual) as total_terjual, 
                                   SUM(j.jumlah_terjual * (h.harga_jual - h.modal)) as keuntungan
                            FROM penjualan j
                            JOIN produk_ban pb ON j.produk_id = pb.id
                            JOIN merek_ban mb ON pb.merek_id = mb.id
                            JOIN harga h ON pb.id = h.produk_id
                            WHERE MONTH(j.tanggal) = ? AND YEAR(j.tanggal) = ?
                            GROUP BY pb.merek_id");
    $stmt->bind_param("ii", $bulan, $tahun);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getProdukById($produk_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT pb.*, h.modal, h.harga_jual, s.jumlah_stok_awal, s.jumlah_stok_sisa, s.stok_minimum 
                            FROM produk_ban pb 
                            JOIN harga h ON pb.id = h.produk_id 
                            JOIN stok s ON pb.id = s.produk_id 
                            WHERE pb.id = ?");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function searchProduk($keyword) {
    global $conn;
    $stmt = $conn->prepare("SELECT pb.nama_produk, s.jumlah_stok_awal, s.jumlah_stok_sisa, s.stok_minimum, pb.id
                            FROM stok s
                            JOIN produk_ban pb ON s.produk_id = pb.id
                            WHERE pb.nama_produk LIKE ? OR pb.ukuran LIKE ? OR pb.ring LIKE ?");
    $keyword = "%$keyword%";
    $stmt->bind_param("sss", $keyword, $keyword, $keyword);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function exportDataToExcel($data, $filename) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Fill data to sheet
    $sheet->fromArray($data, NULL, 'A1');

    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
}

function getPenjualanData() {
    global $conn;
    $sql = "SELECT p.nama_produk, j.tanggal, j.jumlah_terjual
            FROM penjualan j
            JOIN produk_ban p ON j.produk_id = p.id
            ORDER BY j.tanggal DESC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function addAuditLog($action, $item_type, $item_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO audit_trail (action, item_type, item_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $action, $item_type, $item_id);
    $stmt->execute();
}

function getAuditLogs() {
    global $conn;
    $sql = "SELECT * FROM audit_trail ORDER BY timestamp DESC";
    $result = $conn->query($sql);
    return $result;
}

function importDataFromExcel($filePath) {
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    global $conn;
    $errors = [];
    foreach ($data as $index => $row) {
        if ($index == 0) continue; // Skip header row

        // Remove thousand separators and replace comma with dot for decimal
        $row[4] = str_replace(',', '.', str_replace('.', '', $row[4]));
        $row[5] = str_replace(',', '.', str_replace('.', '', $row[5]));

        // Validate row data
        $rowErrors = validateRow($row);
        if (!empty($rowErrors)) {
            $errors[] = ["row" => $index + 1, "errors" => $rowErrors];
            continue; // Skip this row if there are validation errors
        }

        // Validate Merek ID
        if (!validateMerekId($row[1])) {
            $errors[] = ["row" => $index + 1, "errors" => ["Merek ID tidak valid"]];
            continue; // Skip this row if Merek ID is not valid
        }

        $nama_produk = $row[0];
        $merek_id = $row[1];
        $ukuran = $row[2];
        $ring = $row[3];
        $modal = $row[4];
        $harga_jual = $row[5];
        $jumlah_stok_awal = $row[6];
        $jumlah_stok_sisa = $row[7];
        $stok_minimum = $row[8];

        $stmt = $conn->prepare("INSERT INTO produk_ban (nama_produk, merek_id, ukuran, ring) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $nama_produk, $merek_id, $ukuran, $ring);
        $stmt->execute();
        $produk_id = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO harga (produk_id, modal, harga_jual) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $produk_id, $modal, $harga_jual);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO stok (produk_id, jumlah_stok_awal, jumlah_stok_sisa, stok_minimum) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $produk_id, $jumlah_stok_awal, $jumlah_stok_sisa, $stok_minimum);
        $stmt->execute();
    }

    return $errors;
}


function validateRow($row) {
    $errors = [];

    // Validate Nama Produk
    if (empty($row[0])) {
        $errors[] = "Nama produk tidak boleh kosong";
    }

    // Validate Merek ID
    if (!is_numeric($row[1]) || $row[1] <= 0) {
        $errors[] = "Merek ID harus berupa angka positif";
    }

    // Validate Ukuran
    if (empty($row[2])) {
        $errors[] = "Ukuran tidak boleh kosong";
    }

    // Validate Ring
    if (!is_numeric($row[3]) || $row[3] <= 0) {
        $errors[] = "Ring harus berupa angka positif";
    }

    // Validate Modal per Unit
    if (!is_numeric(str_replace(',', '', $row[4])) || $row[4] < 0) {
        $errors[] = "Modal harus berupa angka positif atau nol";
    }

    // Validate Harga Jual per Unit
    if (!is_numeric(str_replace(',', '', $row[5])) || $row[5] < 0) {
        $errors[] = "Harga jual harus berupa angka positif atau nol";
    }

    // Validate Jumlah Stok Awal
    if (!is_numeric($row[6]) || $row[6] < 0) {
        $errors[] = "Jumlah stok awal harus berupa angka positif atau nol";
    }

    // Validate Jumlah Stok Sisa
    if (!is_numeric($row[7]) || $row[7] < 0) {
        $errors[] = "Jumlah stok sisa harus berupa angka positif atau nol";
    }

    // Validate Stok Minimum
    if (!is_numeric($row[8]) || $row[8] < 0) {
        $errors[] = "Stok minimum harus berupa angka positif atau nol";
    }

    return $errors;
}


function validateMerekId($merek_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM merek_ban WHERE id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function getStokByMerek($merek_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT pb.nama_produk, s.jumlah_stok_awal, s.jumlah_stok_sisa, s.stok_minimum
                            FROM stok s
                            JOIN produk_ban pb ON s.produk_id = pb.id
                            WHERE pb.merek_id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function addProduk($nama_produk, $merek_id, $ukuran, $ring, $modal, $harga_jual, $jumlah_stok_awal, $stok_minimum) {
    global $conn;

    // Insert into produk_ban
    $stmt = $conn->prepare("INSERT INTO produk_ban (nama_produk, merek_id, ukuran, ring) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sisi", $nama_produk, $merek_id, $ukuran, $ring);
    $result = $stmt->execute();
    $produk_id = $conn->insert_id;

    // Insert into harga
    $stmt = $conn->prepare("INSERT INTO harga (produk_id, modal, harga_jual) VALUES (?, ?, ?)");
    $stmt->bind_param("idd", $produk_id, $modal, $harga_jual);
    $stmt->execute();

    // Insert into stok
    $stmt = $conn->prepare("INSERT INTO stok (produk_id, jumlah_stok_awal, jumlah_stok_sisa, stok_minimum) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $produk_id, $jumlah_stok_awal, $jumlah_stok_awal, $stok_minimum);
    $stmt->execute();

    return $result;
}

function updateProduk($produk_id, $nama_produk, $ukuran, $ring, $modal, $harga_jual, $jumlah_stok_awal, $stok_minimum) {
    global $conn;

    // Update produk_ban
    $stmt = $conn->prepare("UPDATE produk_ban SET nama_produk = ?, ukuran = ?, ring = ? WHERE id = ?");
    $stmt->bind_param("ssii", $nama_produk, $ukuran, $ring, $produk_id);
    $result = $stmt->execute();

    // Update harga
    $stmt = $conn->prepare("UPDATE harga SET modal = ?, harga_jual = ? WHERE produk_id = ?");
    $stmt->bind_param("dii", $modal, $harga_jual, $produk_id);
    $stmt->execute();

    // Update stok
    $stmt = $conn->prepare("UPDATE stok SET jumlah_stok_awal = ?, stok_minimum = ? WHERE produk_id = ?");
    $stmt->bind_param("iii", $jumlah_stok_awal, $stok_minimum, $produk_id);
    $stmt->execute();

    return $result;
}

function deleteProduk($produk_id) {
    global $conn;

    // Delete from penjualan
    $stmt = $conn->prepare("DELETE FROM penjualan WHERE produk_id = ?");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();

    // Delete from stok
    $stmt = $conn->prepare("DELETE FROM stok WHERE produk_id = ?");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();

    // Delete from harga
    $stmt = $conn->prepare("DELETE FROM harga WHERE produk_id = ?");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();

    // Delete from produk_ban
    $stmt = $conn->prepare("DELETE FROM produk_ban WHERE id = ?");
    $stmt->bind_param("i", $produk_id);
    $result = $stmt->execute();

    return $result;
}

function deleteProdukByMerekId($merek_id) {
    global $conn;

    // Menghapus penjualan terkait produk
    $stmt = $conn->prepare("DELETE j FROM penjualan j JOIN produk_ban pb ON j.produk_id = pb.id WHERE pb.merek_id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();

    // Menghapus stok terkait produk
    $stmt = $conn->prepare("DELETE s FROM stok s JOIN produk_ban pb ON s.produk_id = pb.id WHERE pb.merek_id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();

    // Menghapus harga terkait produk
    $stmt = $conn->prepare("DELETE h FROM harga h JOIN produk_ban pb ON h.produk_id = pb.id WHERE pb.merek_id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();

    // Menghapus produk terkait merek
    $stmt = $conn->prepare("DELETE FROM produk_ban WHERE merek_id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
}

function deleteMerek($merek_id) {
    global $conn;
    // Menghapus produk terkait merek terlebih dahulu
    deleteProdukByMerekId($merek_id);

    // Menghapus merek
    $stmt = $conn->prepare("DELETE FROM merek_ban WHERE id = ?");
    $stmt->bind_param("i", $merek_id);
    $result = $stmt->execute();

    if ($result) {
        addAuditLog('delete', 'merek_ban', $merek_id);
    }

    return $result;
}

function getPenjualanByMerek($merek_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.nama_produk, p.ukuran, p.ring, j.tanggal, j.jumlah_terjual
                            FROM penjualan j
                            JOIN produk_ban p ON j.produk_id = p.id
                            WHERE p.merek_id = ?
                            ORDER BY j.tanggal DESC");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getLaporanKeuntunganByMerek($bulan, $tahun, $merek_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            pb.nama_produk, 
            pb.ukuran, 
            pb.ring, 
            SUM(p.jumlah_terjual) as total_terjual, 
            h.modal, 
            h.harga_jual, 
            (SUM(p.jumlah_terjual) * (h.harga_jual - h.modal)) as keuntungan
        FROM penjualan p
        JOIN produk_ban pb ON p.produk_id = pb.id
        JOIN harga h ON pb.id = h.produk_id
        WHERE MONTH(p.tanggal) = ? AND YEAR(p.tanggal) = ? AND pb.merek_id = ?
        GROUP BY pb.id, pb.nama_produk, pb.ukuran, pb.ring, h.modal, h.harga_jual
    ");
    $stmt->bind_param("iii", $bulan, $tahun, $merek_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getNamaMerekById($merek_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT nama_merek FROM merek_ban WHERE id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['nama_merek'];
}

function getNoteByMerek($merek_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT note FROM notes WHERE merek_id = ?");
    $stmt->bind_param("i", $merek_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['note'];
    }
    return '';
}

function addOrUpdateNoteByMerek($merek_id, $note) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notes (merek_id, note) VALUES (?, ?) ON DUPLICATE KEY UPDATE note = VALUES(note)");
    $stmt->bind_param("is", $merek_id, $note);
    return $stmt->execute();
}


?>


