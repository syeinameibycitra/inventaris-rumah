
<?php
require 'koneksi.php';

function aman($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Pencarian
$cari = trim($_GET['cari'] ?? '');

if ($cari !== '') {
    $stmt = $conn->prepare(
        "SELECT * FROM barang_rumah
         WHERE kode_barang LIKE ?
         OR nama_barang LIKE ?
         OR kategori LIKE ?
         OR lokasi LIKE ?
         ORDER BY id_barang DESC"
    );

    $keyword = "%$cari%";
    $stmt->execute([$keyword, $keyword, $keyword, $keyword]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $data = $conn->query(
        "SELECT * FROM barang_rumah ORDER BY id_barang DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
}

// Ringkasan
$totalBarang = $conn->query(
    "SELECT COUNT(*) FROM barang_rumah"
)->fetchColumn();

$totalStok = $conn->query(
    "SELECT COALESCE(SUM(jumlah),0) FROM barang_rumah"
)->fetchColumn();

$totalNilai = $conn->query(
    "SELECT COALESCE(SUM(jumlah * harga),0) FROM barang_rumah"
)->fetchColumn();

$totalRusak = $conn->query(
    "SELECT COALESCE(SUM(jumlah),0)
     FROM barang_rumah WHERE kondisi='Rusak'"
)->fetchColumn();

?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Sistem Inventaris Rumah</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
rel="stylesheet">

<style>
body {
    background: #f3f6f2;
    font-family: 'Segoe UI', sans-serif;
    color: #34483d;
}

.sidebar {
    background: #304c3d;
    min-height: 100vh;
    padding: 28px 18px;
    color: white;
}

.logo {
    font-size: 21px;
    font-weight: bold;
    margin-bottom: 38px;
}

.menu {
    display: block;
    text-decoration: none;
    color: #dce8df;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 9px;
}

.menu.active,
.menu:hover {
    background: #607d68;
    color: white;
}

.main {
    padding: 30px;
}

.header {
    background: white;
    padding: 22px;
    border-radius: 16px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px #304c3d0a;
}

.summary {
    padding: 23px;
    border-radius: 16px;
    color: white;
    min-height: 145px;
}

.satu {
    background: linear-gradient(135deg,#466b54,#7e9f83);
}

.dua {
    background: linear-gradient(135deg,#477d75,#82b6a4);
}

.tiga {
    background: linear-gradient(135deg,#b18453,#d8b783);
}

.empat {
    background: linear-gradient(135deg,#a75b57,#d68b80);
}

.panel {
    background: white;
    padding: 25px;
    border-radius: 16px;
    margin-top: 25px;
    box-shadow: 0 4px 15px #304c3d0a;
}

.table thead th {
    background: #e9f0e9;
    color: #34483d;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.btn-success {
    background: #466b54;
    border: none;
}

.btn-success:hover {
    background: #304c3d;
}

.form-control,
.form-select {
    border-radius: 9px;
    padding: 10px;
}

@media(max-width:767px) {
    .sidebar {
        min-height: auto;
    }

    .main {
        padding: 15px;
    }
}
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-md-2 sidebar">

    <div class="logo">
        <i class="bi bi-house-heart-fill"></i>
        RumahKu
    </div>

    <a href="index.php" class="menu active">
        <i class="bi bi-grid-fill me-2"></i>
        Dashboard
    </a>

    <a href="tambah.php" class="menu">
        <i class="bi bi-plus-circle me-2"></i>
        Tambah Barang
    </a>

    <a href="#daftar" class="menu">
        <i class="bi bi-box-seam me-2"></i>
        Daftar Barang
    </a>

    <hr>

    <small class="text-white-50">
        INVENTARIS RUMAH
    </small>

</div>

<!-- KONTEN UTAMA -->
<div class="col-md-10 main">

    <div class="header d-flex justify-content-between align-items-center">

        <div>
            <h3 class="fw-bold mb-1">
                Dashboard Inventaris Rumah
            </h3>

            <p class="text-muted mb-0">
                Kelola dan pantau barang rumah tangga Anda.
            </p>
        </div>

        <div class="text-end">
            <i class="bi bi-house-check fs-2"></i>
            <div><small>Administrator</small></div>
        </div>

    </div>

    <!-- RINGKASAN -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="summary satu">
                <i class="bi bi-box-seam fs-3"></i>
                <p class="mt-2 mb-1">Jenis Barang</p>
                <h2 class="fw-bold"><?= $totalBarang ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary dua">
                <i class="bi bi-boxes fs-3"></i>
                <p class="mt-2 mb-1">Total Stok</p>
                <h2 class="fw-bold"><?= $totalStok ?></h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary tiga">
                <i class="bi bi-cash-stack fs-3"></i>
                <p class="mt-2 mb-1">Nilai Barang</p>
                <h5 class="fw-bold mt-2"><?= rupiah($totalNilai) ?></h5>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary empat">
                <i class="bi bi-exclamation-circle fs-3"></i>
                <p class="mt-2 mb-1">Barang Rusak</p>
                <h2 class="fw-bold"><?= $totalRusak ?></h2>
            </div>
        </div>

    </div>

    <!-- DAFTAR BARANG -->
    <div class="panel" id="daftar">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

            <h5 class="fw-bold mb-0">
                <i class="bi bi-list-ul me-2"></i>
                Daftar Barang Rumah
            </h5>

            <a href="tambah.php" class="btn btn-success">
                <i class="bi bi-plus-lg"></i>
                Tambah Barang
            </a>

        </div>

        <!-- PENCARIAN -->
        <form method="GET" class="row g-2 mb-3">

            <div class="col-md-5">
                <input type="text"
                name="cari"
                class="form-control"
                placeholder="Cari nama, kode, kategori, lokasi..."
                value="<?= aman($cari) ?>">
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-search"></i>
                    Cari
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </form>

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>Lokasi</th>
                        <th>Harga</th>
                        <th>Tanggal Beli</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (count($data) > 0): ?>

                    <?php $no = 1; ?>

                    <?php foreach ($data as $row): ?>

                    <tr>
                        <td><?= $no++ ?></td>

                        <td><?= aman($row['kode_barang']) ?></td>

                        <td><?= aman($row['nama_barang']) ?></td>

                        <td><?= aman($row['kategori']) ?></td>

                        <td><?= aman($row['jumlah']) ?></td>

                        <td>
                            <?php
                            if ($row['kondisi'] == 'Baik') {
                                $warna = 'success';
                            } elseif ($row['kondisi'] == 'Rusak') {
                                $warna = 'danger';
                            } else {
                                $warna = 'warning text-dark';
                            }
                            ?>
                            <span class="badge bg-<?= $warna ?>">
                                <?= aman($row['kondisi']) ?>
                            </span>
                        </td>

                        <td><?= aman($row['lokasi']) ?></td>

                        <td><?= rupiah($row['harga']) ?></td>

                        <td><?= aman($row['tanggal_beli']) ?></td>

                        <td><?= aman($row['keterangan']) ?></td>

                        <td>
                            <div class="d-flex gap-1">

                                <a href="edit.php?id=<?= (int)$row['id_barang'] ?>"
                                class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                    Edit
                                </a>

                                <a href="hapus.php?id=<?= (int)$row['id_barang'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                    <i class="bi bi-trash"></i>
                                    Hapus
                                </a>

                            </div>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="11" class="text-center py-4">
                            Belum ada data barang.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <footer class="text-center text-muted mt-4 mb-3">
        Sistem Inventaris Rumah Tangga &copy; 2026
    </footer>

</div>
</div>
</div>

</body>
</html>
