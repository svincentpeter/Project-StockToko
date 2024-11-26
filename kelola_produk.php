<?php
include 'functions.php';
include 'navbar.php';

$merek_ban = getMerek();
$produk_ban = [];
$note = '';

if (isset($_POST['merek_id'])) {
    $merek_id = $_POST['merek_id'];
    $produk_ban = getProdukByMerek($merek_id);
    $note = getNoteByMerek($merek_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $nama_produk = $_POST['nama_produk'];
        $merek_id = $_POST['merek_id'];
        $ukuran = $_POST['ukuran'];
        $ring = $_POST['ring'];
        $modal = $_POST['modal'];
        $harga_jual = $_POST['harga_jual'];
        $jumlah_stok_awal = $_POST['jumlah_stok_awal'];
        $stok_minimum = $_POST['stok_minimum'];

        $result = addProduk($nama_produk, $merek_id, $ukuran, $ring, $modal, $harga_jual, $jumlah_stok_awal, $stok_minimum);
        $message = $result ? "Produk baru berhasil ditambahkan!" : "Terjadi kesalahan dalam menambahkan produk.";
    } elseif ($_POST['action'] == 'edit') {
        $produk_id = $_POST['produk_id'];
        $nama_produk = $_POST['nama_produk'];
        $ukuran = $_POST['ukuran'];
        $ring = $_POST['ring'];
        $modal = $_POST['modal'];
        $harga_jual = $_POST['harga_jual'];
        $jumlah_stok_awal = $_POST['jumlah_stok_awal'];
        $stok_minimum = $_POST['stok_minimum'];

        $result = updateProduk($produk_id, $nama_produk, $ukuran, $ring, $modal, $harga_jual, $jumlah_stok_awal, $stok_minimum);
        $message = $result ? "Produk berhasil diperbarui!" : "Terjadi kesalahan dalam memperbarui produk.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['note_action'])) {
    if ($_POST['note_action'] == 'save_note') {
        $merek_id = $_POST['merek_id'];
        $note = $_POST['note'];
        $result = addOrUpdateNoteByMerek($merek_id, $note);
        $message = $result ? "Catatan berhasil disimpan!" : "Terjadi kesalahan dalam menyimpan catatan.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete_id'])) {
    $produk_id = $_GET['delete_id'];
    $result = deleteProduk($produk_id);
    $message = $result ? "Produk berhasil dihapus!" : "Terjadi kesalahan dalam menghapus produk.";
}

function getProdukDetails($produk_id) {
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Omah Ban</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Kelola Produk</h1>
        <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>

        <form method="POST" action="kelola_produk.php" class="form-group">
            <label for="merek_id">Pilih Merek:</label>
            <select class="form-control" id="merek_id" name="merek_id" onchange="this.form.submit()">
                <option value="">Pilih Merek</option>
                <?php while($merek = $merek_ban->fetch_assoc()) { ?>
                    <option value="<?php echo $merek['id']; ?>" <?php if (isset($merek_id) && $merek_id == $merek['id']) echo 'selected'; ?>><?php echo $merek['nama_merek']; ?></option>
                <?php } ?>
            </select>
        </form>

        <?php if (!empty($produk_ban)) { ?>
        <h2 class="mt-4">Daftar Produk</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Ukuran</th>
                        <th>Ring</th>
                        <th>Modal</th>
                        <th>Harga Jual</th>
                        <th>Stok Awal</th>
                        <th>Stok Sisa</th>
                        <th>Stok Minimum</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($produk = $produk_ban->fetch_assoc()) { 
                        $details = getProdukDetails($produk['id']); ?>
                    <tr>
                        <td><?php echo $produk['nama_produk']; ?></td>
                        <td><?php echo $details['ukuran']; ?></td>
                        <td><?php echo $details['ring']; ?></td>
                        <td><?php echo $details['modal']; ?></td>
                        <td><?php echo $details['harga_jual']; ?></td>
                        <td><?php echo $details['jumlah_stok_awal']; ?></td>
                        <td><?php echo $details['jumlah_stok_sisa']; ?></td>
                        <td><?php echo $details['stok_minimum']; ?></td>
                        <td>
                            <a href="javascript:void(0);" class="btn btn-warning btn-sm" onclick="editProduk(
                                <?php echo $produk['id']; ?>, 
                                '<?php echo $produk['nama_produk']; ?>', 
                                '<?php echo $details['ukuran']; ?>', 
                                '<?php echo $details['ring']; ?>', 
                                '<?php echo $details['modal']; ?>', 
                                '<?php echo $details['harga_jual']; ?>', 
                                '<?php echo $details['jumlah_stok_awal']; ?>', 
                                '<?php echo $details['stok_minimum']; ?>')">Edit</a>
                            <a href="kelola_produk.php?delete_id=<?php echo $produk['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                        </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else if (isset($merek_id)) { ?>
            <p>Tidak ada produk untuk merek yang dipilih.</p>
        <?php } ?>

        <h2 class="mt-4">Catatan untuk Merek: <?php echo isset($merek_id) ? getMerekNameById($merek_id) : ''; ?></h2>
        <form method="POST" action="kelola_produk.php" class="form-group">
            <input type="hidden" name="note_action" value="save_note">
            <input type="hidden" name="merek_id" value="<?php echo isset($merek_id) ? $merek_id : ''; ?>">
            <textarea id="summernote" name="note"><?php echo isset($note) ? $note : ''; ?></textarea>
            <button type="submit" class="btn btn-primary mt-2">Simpan Catatan</button>
        </form>

        <button type="button" class="btn btn-primary mt-4" data-toggle="modal" data-target="#addProductModal">
            Tambah Produk Baru
        </button>

        <!-- Modal Tambah Produk -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="kelola_produk.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Tambah Produk Baru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="add">
                            <div class="form-group">
                                <label for="nama_produk">Nama Produk:</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="merek_id_add">Merek:</label>
                                <select class="form-control" id="merek_id_add" name="merek_id" required>
                                    <option value="">Pilih Merek</option>
                                    <?php foreach ($merek_ban as $merek) { ?>
                                        <option value="<?php echo $merek['id']; ?>"><?php echo $merek['nama_merek']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ukuran">Ukuran:</label>
                                <input type="text" class="form-control" id="ukuran" name="ukuran" required>
                            </div>
                            <div class="form-group">
                                <label for="ring">Ring:</label>
                                <input type="text" class="form-control" id="ring" name="ring" required>
                            </div>
                            <div class="form-group">
                                <label for="modal">Modal:</label>
                                <input type="number" step="0.01" class="form-control" id="modal" name="modal" required>
                            </div>
                            <div class="form-group">
                                <label for="harga_jual">Harga Jual:</label>
                                <input type="number" step="0.01" class="form-control" id="harga_jual" name="harga_jual" required>
                            </div>
                            <div class="form-group">
                                <label for="jumlah_stok_awal">Jumlah Stok Awal:</label>
                                <input type="number" class="form-control" id="jumlah_stok_awal" name="jumlah_stok_awal" required>
                            </div>
                            <div class="form-group">
                                <label for="stok_minimum">Stok Minimum:</label>
                                <input type="number" class="form-control" id="stok_minimum" name="stok_minimum" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Produk -->
        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="kelola_produk.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProductModalLabel">Edit Produk</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" id="edit_produk_id" name="produk_id">
                            <div class="form-group">
                                <label for="edit_nama_produk">Nama Produk:</label>
                                <input type="text" class="form-control" id="edit_nama_produk" name="nama_produk" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_ukuran">Ukuran:</label>
                                <input type="text" class="form-control" id="edit_ukuran" name="ukuran" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_ring">Ring:</label>
                                <input type="text" class="form-control" id="edit_ring" name="ring" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_modal">Modal:</label>
                                <input type="number" step="0.01" class="form-control" id="edit_modal" name="modal" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_harga_jual">Harga Jual:</label>
                                <input type="number" step="0.01" class="form-control" id="edit_harga_jual" name="harga_jual" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_jumlah_stok_awal">Jumlah Stok Awal:</label>
                                <input type="number" class="form-control" id="edit_jumlah_stok_awal" name="jumlah_stok_awal" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_stok_minimum">Stok Minimum:</label>
                                <input type="number" class="form-control" id="edit_stok_minimum" name="stok_minimum" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Perbarui Produk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editProduk(id, nama, ukuran, ring, modal, harga_jual, stok_awal, stok_minimum) {
            document.getElementById('edit_produk_id').value = id;
            document.getElementById('edit_nama_produk').value = nama;
            document.getElementById('edit_ukuran').value = ukuran;
            document.getElementById('edit_ring').value = ring;
            document.getElementById('edit_modal').value = modal;
            document.getElementById('edit_harga_jual').value = harga_jual;
            document.getElementById('edit_jumlah_stok_awal').value = stok_awal;
            document.getElementById('edit_stok_minimum').value = stok_minimum;
            $('#editProductModal').modal('show');
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ]
            });
        });
    </script>
</body>

</html>

