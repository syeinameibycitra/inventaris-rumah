
<?php
require 'koneksi.php';

$error = '';

function aman($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['simpan'])) {

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
            $sql = "INSERT INTO barang_rumah
            (kode_barang, nama_barang, kategori, jumlah,
            kondisi, lokasi, harga, tanggal_beli, keterangan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                $kode, $nama, $kategori, $jumlah,
                $kondisi, $lokasi, $harga,
                $tanggal, $keterangan
            ]);

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Kode barang sudah digunakan atau data gagal disimpan.";
        }

    } else {
        $error = "Mohon isi semua data dengan benar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tambah Barang Rumah</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>
body {
    background: #f3f6f2;
    font-family: 'Segoe UI', sans-serif;
}

.form-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
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
                <h3 class="fw-bold">Tambah Barang Rumah</h3>
                <p class="mb-0">Masukkan data inventaris rumah tangga.</p>
            </div>

            <div class="form-card">

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
                            class="form-control" placeholder="Contoh RMH011" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang"
                            class="form-control" placeholder="Nama barang" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih kategori</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Perabotan">Perabotan</option>
                                <option value="Peralatan Dapur">Peralatan Dapur</option>
                                <option value="Kebersihan">Kebersihan</option>
                                <option value="Peralatan Kamar Tidur">Peralatan Kamar Tidur</option>
                                <option value="Peralatan Kamar Mandi">Peralatan Kamar Mandi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Barang</label>
                            <input type="number" name="jumlah"
                            class="form-control" min="0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kondisi Barang</label>
                            <select name="kondisi" class="form-select" required>
                                <option value="Baik">Baik</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Lokasi Barang</label>
                            <input type="text" name="lokasi"
                            class="form-control" placeholder="Contoh: Ruang Tamu" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Harga Barang (Rp)</label>
                            <input type="number" name="harga"
                            class="form-control" min="0" step="0.01" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pembelian</label>
                            <input type="date" name="tanggal_beli"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan"
                            class="form-control" rows="3"
                            placeholder="Keterangan tambahan barang"></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" name="simpan"
                            class="btn btn-success px-4">
                                <i class="bi bi-save"></i>
                                Simpan Barang
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
