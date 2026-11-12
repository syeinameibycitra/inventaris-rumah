
<?php
require 'koneksi.php';

function aman($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare(
    "SELECT * FROM barang_rumah WHERE id_barang = ?"
);
$stmt->execute([$id]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data barang tidak ditemukan!");
}

$error = '';

if (isset($_POST['update'])) {

    $kode = trim($_POST['kode_barang']);
    $nama = trim($_POST['nama_barang']);
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $kondisi = $_POST['kondisi'];
    $lokasi = trim($_POST['lokasi']);
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal_beli'];
    $keterangan = trim($_POST['keterangan']);

    if (
        $kode !== '' &&
        $nama !== '' &&
        $kategori !== '' &&
        filter_var($jumlah, FILTER_VALIDATE_INT) !== false &&
        $jumlah >= 0 &&
        in_array($kondisi, ['Baik', 'Rusak', 'Perlu Perbaikan']) &&
        $lokasi !== '' &&
        is_numeric($harga) &&
        $harga >= 0 &&
        $tanggal !== ''
    ) {
        try {
            $sql = "UPDATE barang_rumah SET
                kode_barang = ?,
                nama_barang = ?,
                kategori = ?,
                jumlah = ?,
                kondisi = ?,
                lokasi = ?,
                harga = ?,
                tanggal_beli = ?,
                keterangan = ?
                WHERE id_barang = ?";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                $kode, $nama, $kategori, $jumlah,
                $kondisi, $lokasi, $harga,
                $tanggal, $keterangan, $id
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Kode barang sudah digunakan atau data gagal diperbarui.";
        }

    } else {
        $error = "Mohon isi semua data dengan benar!";
    }
}

$kategoriList = [
    'Elektronik',
    'Perabotan',
    'Peralatan Dapur',
    'Kebersihan',
    'Peralatan Kamar Tidur',
    'Peralatan Kamar Mandi',
    'Lainnya'
];

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Barang Rumah</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>
body {
    background: #f3f6f2;
    font-family: 'Segoe UI', sans-serif;
}

.edit-card {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 15px #304c3d0a;
}

.header-box {
    background: #304c3d;
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 25px;
}

.form-control, .form-select {
    padding: 11px;
    border-radius: 9px;
}

.btn-success {
    background: #466b54;
    border: none;
}
</style>
</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="header-box">
                <h3 class="fw-bold">Edit Data Barang</h3>
                <p class="mb-0">Perbarui informasi inventaris rumah.</p>
            </div>

            <div class="edit-card">

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= aman($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" name="kode_barang"
                            class="form-control"
                            value="<?= aman($data['kode_barang']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang"
                            class="form-control"
                            value="<?= aman($data['nama_barang']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <?php foreach ($kategoriList as $k): ?>
                                    <option value="<?= aman($k) ?>"
                                    <?= $data['kategori'] == $k ? 'selected' : '' ?>>
                                        <?= aman($k) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Barang</label>
                            <input type="number" name="jumlah"
                            class="form-control" min="0"
                            value="<?= aman($data['jumlah']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kondisi Barang</label>
                            <select name="kondisi" class="form-select" required>
                                <?php foreach (['Baik','Rusak','Perlu Perbaikan'] as $k): ?>
                                    <option value="<?= aman($k) ?>"
                                    <?= $data['kondisi'] == $k ? 'selected' : '' ?>>
                                        <?= aman($k) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Lokasi Barang</label>
                            <input type="text" name="lokasi"
                            class="form-control"
                            value="<?= aman($data['lokasi']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Harga Barang (Rp)</label>
                            <input type="number" name="harga"
                            class="form-control" min="0" step="0.01"
                            value="<?= aman($data['harga']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pembelian</label>
                            <input type="date" name="tanggal_beli"
                            class="form-control"
                            value="<?= aman($data['tanggal_beli']) ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan"
                            class="form-control" rows="3"><?= aman($data['keterangan']) ?></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" name="update"
                            class="btn btn-success px-4">
                                Simpan Perubahan
                            </button>

                            <a href="index.php"
                            class="btn btn-secondary px-4">
                                Kembali
                            </a>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

</body>
</html>
