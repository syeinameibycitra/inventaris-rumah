
<?php
require 'koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare(
    "DELETE FROM barang_rumah WHERE id_barang = ?"
);

$stmt->execute([$id]);

header("Location: index.php");
exit;
?>
