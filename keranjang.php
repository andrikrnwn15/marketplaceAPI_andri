<?php require 'config/koneksi.php';
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $aksi = $_POST['aksi'] ?? ''; $id = (int)($_POST['id'] ?? 0); $qty = max(1, (int)($_POST['qty'] ?? 1)); $to = 'keranjang.php';
  if ($aksi === 'tambah' && $id) {
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty; $_SESSION['flash'] = '✓ Produk ditambahkan ke keranjang';
    if (!empty($_POST['langsung'])) { header('Location: checkout.php'); exit; }
    $back = $_POST['back'] ?? '';
    if ($back !== '' && preg_match('/^[a-z_\.\/]+(\?[\w=&%+\-\.]*)?$/i', $back)) { $to = $back; }
  }
  if ($aksi === 'ubah' && $id) { $_SESSION['cart'][$id] = $qty; }
  if ($aksi === 'hapus') { unset($_SESSION['cart'][$id]); }
  header('Location: ' . $to); exit;
}
include 'includes/header.php';
[$items, $total] = ambilKeranjang($conn);
?>
<div class="sect">Keranjang</div>
<?php if (!$items): ?>
<div class="box text-center py-5"><div style="font-size:60px">🛒</div><h5 class="fw-bold">Wah, keranjang belanjamu kosong</h5>
  <p class="text-muted">Yuk, isi dengan produk-produk segar dari pasar!</p><a href="index.php" class="btn btn-g">Mulai Belanja</a></div>
<?php else: ?>
<div class="row g-4">
  <div class="col-lg-8"><div class="box">
    <?php foreach ($items as $i): ?>
    <div class="d-flex gap-3 align-items-center py-3 border-bottom">
      <div style="width:84px;flex:none;border-radius:8px;overflow:hidden"><?= thumb($i, 84) ?></div>
      <div class="flex-grow-1">
        <div class="meta">🏪 <?= e($i['nama_toko']) ?></div>
        <a href="produk_detail.php?id=<?= $i['id_produk'] ?>" class="text-decoration-none fw-semibold"><?= e($i['nama_produk']) ?></a>
        <div class="fw-bold"><?= rupiah($i['harga']) ?></div>
      </div>
      <div class="text-end">
        <form method="post" class="d-inline"><input type="hidden" name="aksi" value="ubah"><input type="hidden" name="id" value="<?= $i['id_produk'] ?>">
          <div class="stepper"><input type="number" name="qty" value="<?= $i['qty'] ?>" min="1" max="<?= (int)$i['stok'] ?>" onchange="this.form.submit()"></div></form>
        <form method="post" class="d-inline"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $i['id_produk'] ?>">
          <button class="btn btn-sm text-danger" title="Hapus">🗑️</button></form>
        <div class="meta mt-1">Subtotal <strong><?= rupiah($i['sub']) ?></strong></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div></div>
  <div class="col-lg-4"><div class="box buy">
    <h6 class="fw-bold">Ringkasan belanja</h6>
    <div class="d-flex justify-content-between my-3"><span class="text-muted">Total Harga (<?= array_sum(array_column($items, 'qty')) ?> barang)</span><strong class="fs-5"><?= rupiah($total) ?></strong></div>
    <a href="checkout.php" class="btn btn-g w-100 mb-2">Checkout</a>
    <a href="index.php" class="btn btn-outline-secondary w-100">Lanjut Belanja</a>
  </div></div>
</div>
<?php endif; include 'includes/footer.php'; ?>
