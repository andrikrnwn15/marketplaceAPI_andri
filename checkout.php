<?php require 'config/koneksi.php';
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { header('Location: keranjang.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama'] ?? ''); $email = trim($_POST['email'] ?? '');
  $telp = trim($_POST['telepon'] ?? ''); $alamat = trim($_POST['alamat'] ?? '');
  if ($nama === '' || $telp === '' || $alamat === '') { $error = 'Nama, telepon, dan alamat wajib diisi.'; }
  else {
    $conn->begin_transaction();
    try {
      $total = 0; $baris = [];
      foreach ($cart as $id => $qty) {
        $st = $conn->prepare("SELECT harga, stok, nama_produk FROM produk WHERE id_produk=? FOR UPDATE");
        $st->bind_param('i', $id); $st->execute(); $p = $st->get_result()->fetch_assoc();
        if (!$p || $p['stok'] < $qty) throw new Exception('Stok tidak cukup untuk ' . ($p['nama_produk'] ?? 'produk'));
        $sub = $p['harga'] * $qty; $total += $sub; $baris[] = [$id, $qty, $p['harga'], $sub];
      }
      $st = $conn->prepare("INSERT INTO pembeli (nama,email,telepon,alamat) VALUES (?,?,?,?)");
      $st->bind_param('ssss', $nama, $email, $telp, $alamat); $st->execute(); $idPembeli = $conn->insert_id;
      $st = $conn->prepare("INSERT INTO pesanan (id_pembeli,total) VALUES (?,?)");
      $st->bind_param('id', $idPembeli, $total); $st->execute(); $idPesanan = $conn->insert_id;
      $sd = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan,id_produk,jumlah,harga_satuan,subtotal) VALUES (?,?,?,?,?)");
      $su = $conn->prepare("UPDATE produk SET stok=stok-? WHERE id_produk=?");
      foreach ($baris as [$id, $qty, $harga, $sub]) {
        $sd->bind_param('iiidd', $idPesanan, $id, $qty, $harga, $sub); $sd->execute();
        $su->bind_param('ii', $qty, $id); $su->execute();
      }
      $conn->commit(); $_SESSION['cart'] = [];
      header('Location: sukses.php?id=' . $idPesanan); exit;
    } catch (Exception $ex) { $conn->rollback(); $error = $ex->getMessage(); }
  }
}
include 'includes/header.php';
[$items, $total] = ambilKeranjang($conn); ?>
<div class="sect">Checkout</div>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="row g-4">
  <div class="col-lg-8"><div class="box row g-3">
    <h6 class="fw-bold">Alamat Pengiriman</h6>
    <div class="col-md-6"><label class="form-label">Nama Penerima</label><input name="nama" class="form-control" value="<?= e($_POST['nama'] ?? '') ?>" required></div>
    <div class="col-md-6"><label class="form-label">No. Telepon</label><input name="telepon" class="form-control" value="<?= e($_POST['telepon'] ?? '') ?>" required></div>
    <div class="col-12"><label class="form-label">Email (opsional)</label><input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>"></div>
    <div class="col-12"><label class="form-label">Alamat Lengkap</label><textarea name="alamat" rows="3" class="form-control" required><?= e($_POST['alamat'] ?? '') ?></textarea></div>
    <h6 class="fw-bold mt-4">Barang yang dibeli</h6>
    <?php foreach ($items as $i): ?>
    <div class="d-flex gap-3 align-items-center border-top pt-3">
      <div style="width:56px;flex:none;border-radius:8px;overflow:hidden"><?= thumb($i, 56) ?></div>
      <div class="flex-grow-1"><div class="fw-semibold"><?= e($i['nama_produk']) ?></div><div class="meta"><?= $i['qty'] ?> x <?= rupiah($i['harga']) ?></div></div>
      <strong><?= rupiah($i['sub']) ?></strong></div>
    <?php endforeach; ?>
  </div></div>
  <div class="col-lg-4"><div class="box buy">
    <h6 class="fw-bold">Ringkasan belanja</h6>
    <div class="d-flex justify-content-between my-3"><span class="text-muted">Total Tagihan</span><strong class="fs-5"><?= rupiah($total) ?></strong></div>
    <button class="btn btn-g w-100 mb-2">Buat Pesanan</button>
    <a href="keranjang.php" class="btn btn-outline-secondary w-100">Kembali</a>
  </div></div>
</form>
<?php include 'includes/footer.php'; ?>
