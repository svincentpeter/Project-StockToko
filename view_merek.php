<?php
include 'functions.php';
include 'navbar.php';

// Tambah Merek
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_merek'])) {
    $nama_merek = trim($_POST['nama_merek']);
    if (empty($nama_merek)) {
        $message = "Nama Merek harus diisi";
    } else {
        if (addMerek($nama_merek)) {
            $message = "Merek baru berhasil ditambahkan!";
        } else {
            $message = "Terjadi kesalahan dalam menambahkan merek.";
        }
    }
}

// Edit Merek
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_merek'])) {
    $merek_id = $_POST['merek_id'];
    $nama_merek = trim($_POST['nama_merek']);
    if (empty($nama_merek)) {
        $message = "Nama Merek harus diisi";
    } else {
        if (updateMerek($merek_id, $nama_merek)) {
            $message = "Merek berhasil diperbarui!";
        } else {
            $message = "Terjadi kesalahan dalam memperbarui merek.";
        }
    }
}

// Hapus Merek
if (isset($_GET['delete_id'])) {
    $merek_id = $_GET['delete_id'];
    if (deleteMerek($merek_id)) {
        $message = "Merek berhasil dihapus!";
    } else {
        $message = "Terjadi kesalahan dalam menghapus merek.";
    }
}

$merek = getMerek();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Merek - Omah Ban</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Daftar Merek</h1>
        <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>

        <button type="button" class="btn btn-primary mt-4" data-toggle="modal" data-target="#addMerekModal">
            Tambah Merek Baru
        </button>

        <h2 class="mt-4">Daftar Merek</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Merek</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $merek->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nama_merek']; ?></td>
                    <td>
                        <a href="javascript:void(0);" class="btn btn-warning btn-sm" onclick="editMerek(<?php echo $row['id']; ?>, '<?php echo $row['nama_merek']; ?>')">Edit</a>
                        <a href="view_merek.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus merek ini?');">Hapus</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Modal Tambah Merek -->
        <div class="modal fade" id="addMerekModal" tabindex="-1" aria-labelledby="addMerekModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="view_merek.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addMerekModalLabel">Tambah Merek Baru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="add_merek" value="1">
                            <div class="form-group">
                                <label for="nama_merek">Nama Merek:</label>
                                <input type="text" class="form-control" id="nama_merek" name="nama_merek" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah Merek</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Merek -->
        <div class="modal fade" id="editMerekModal" tabindex="-1" aria-labelledby="editMerekModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="view_merek.php">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMerekModalLabel">Edit Merek</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="edit_merek" value="1">
                            <input type="hidden" id="merek_id" name="merek_id">
                            <div class="form-group">
                                <label for="edit_nama_merek">Nama Merek:</label>
                                <input type="text" class="form-control" id="edit_nama_merek" name="nama_merek" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Perbarui Merek</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function editMerek(id, nama) {
            document.getElementById('merek_id').value = id;
            document.getElementById('edit_nama_merek').value = nama;
            $('#editMerekModal').modal('show');
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
