<?php require 'config/koneksi.php'; include 'includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT ps.*, b.nama, b.alamat FROM pesanan ps JOIN pembeli b ON ps.id_pembeli=b.id_pembeli WHERE ps.id_pesanan=?");
$st->bind_param('i', $id); $st->execute(); $ps = $st->get_result()->fetch_assoc();
if (!$ps) { echo '<div class="alert alert-danger">Pesanan tidak ditemukan.</div>'; include 'includes/footer.php'; exit; }
$d = $conn->query("SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON d.id_produk=p.id_produk WHERE d.id_pesanan=$id");
?>
<div class="box mx-auto" style="max-width:720px">
  <div class="text-center mb-4"><div style="font-size:60px">✅</div><h4 class="fw-bold">Pesanan berhasil dibuat!</h4>
    <p class="text-muted mb-0">Nomor pesanan <strong>#<?= $ps['id_pesanan'] ?></strong> &middot; <?= e($ps['tanggal']) ?></p></div>
  <p class="mb-1"><span class="text-muted">Penerima:</span> <strong><?= e($ps['nama']) ?></strong></p>
  <p><span class="text-muted">Alamat:</span> <?= e($ps['alamat']) ?></p>
  <table class="table"><thead><tr><th>Produk</th><th class="text-center">Jml</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead><tbody>
  <?php while ($r = $d->fetch_assoc()): ?>
  <tr><td><?= e($r['nama_produk']) ?></td><td class="text-center"><?= $r['jumlah'] ?></td><td class="text-end"><?= rupiah($r['harga_satuan']) ?></td><td class="text-end"><?= rupiah($r['subtotal']) ?></td></tr>
  <?php endwhile; ?></tbody>
  <tfoot><tr><th colspan="3" class="text-end">Total</th><th class="text-end"><?= rupiah($ps['total']) ?></th></tr></tfoot></table>
  <div class="text-center"><a href="index.php" class="btn btn-g">Belanja Lagi</a></div>
</div>
<?php include 'includes/footer.php'; ?>
