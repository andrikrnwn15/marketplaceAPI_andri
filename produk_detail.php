<?php require 'config/koneksi.php'; include 'includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT p.*, k.nama_kategori, s.nama_toko, s.alamat AS alamat_toko FROM produk p
  JOIN kategori k ON p.id_kategori=k.id_kategori JOIN penjual s ON p.id_penjual=s.id_penjual WHERE p.id_produk=?");
$st->bind_param('i', $id); $st->execute(); $p = $st->get_result()->fetch_assoc();
if (!$p) { echo '<div class="alert alert-danger">Produk tidak ditemukan.</div>'; include 'includes/footer.php'; exit; }
$st = $conn->prepare("SELECT p.*, s.nama_toko FROM produk p JOIN penjual s ON p.id_penjual=s.id_penjual WHERE p.id_kategori=? AND p.id_produk<>? LIMIT 5");
$st->bind_param('ii', $p['id_kategori'], $id); $st->execute(); $lain = $st->get_result();
$stok = (int)$p['stok'];
?>
<nav class="small text-muted mb-3"><a href="index.php" class="text-decoration-none">Beranda</a> &rsaquo; <a href="index.php?k=<?= $p['id_kategori'] ?>" class="text-decoration-none"><?= e($p['nama_kategori']) ?></a> &rsaquo; <?= e($p['nama_produk']) ?></nav>
<div class="row g-4">
  <div class="col-lg-4"><div class="box p-2"><?= thumb($p, 360) ?></div></div>
  <div class="col-lg-5"><div class="box">
    <h4 class="fw-bold"><?= e($p['nama_produk']) ?></h4>
    <div class="bigprice mb-3"><?= rupiah($p['harga']) ?></div>
    <hr>
    <p class="mb-1"><span class="text-muted">Kategori:</span> <strong class="text-success"><?= e($p['nama_kategori']) ?></strong></p>
    <p class="mb-3"><span class="text-muted">Stok:</span> <strong><?= $stok ?></strong></p>
    <h6 class="fw-bold">Deskripsi Produk</h6>
    <p><?= nl2br(e($p['deskripsi'])) ?></p>
    <hr><div class="d-flex align-items-center gap-3"><div class="fs-2">🏪</div>
      <div><strong><?= e($p['nama_toko']) ?></strong><div class="meta">📍 <?= e($p['alamat_toko']) ?></div></div></div>
  </div></div>
  <div class="col-lg-3"><div class="box buy">
    <h6 class="fw-bold mb-3">Atur jumlah pembelian</h6>
    <?php if ($stok > 0): ?>
    <form method="post" action="keranjang.php">
      <input type="hidden" name="aksi" value="tambah"><input type="hidden" name="id" value="<?= $p['id_produk'] ?>">
      <div class="d-flex align-items-center gap-2 mb-3">
        <div class="stepper"><button type="button" onclick="upd(-1)">&minus;</button><input id="qty" name="qty" value="1" type="number" min="1" max="<?= $stok ?>"><button type="button" onclick="upd(1)">+</button></div>
        <span class="meta">Stok <?= $stok ?></span></div>
      <div class="d-flex justify-content-between mb-3"><span class="text-muted">Subtotal</span><strong id="sub"><?= rupiah($p['harga']) ?></strong></div>
      <button class="btn btn-g w-100 mb-2">+ Keranjang</button>
      <button name="langsung" value="1" class="btn btn-outline-success w-100 fw-bold">Beli Langsung</button>
    </form>
    <script>
      const q=document.getElementById('qty'),sub=document.getElementById('sub'),h=<?= (float)$p['harga'] ?>,mx=<?= $stok ?>;
      function upd(d){let v=Math.min(mx,Math.max(1,(parseInt(q.value)||1)+d));q.value=v;sub.textContent='Rp '+(v*h).toLocaleString('id-ID');}
      q.addEventListener('input',()=>upd(0));
    </script>
    <?php else: ?><div class="alert alert-warning mb-0">Stok habis.</div><?php endif; ?>
  </div></div>
</div>
<?php if ($lain->num_rows): ?>
<div class="sect mt-5">Produk Serupa</div>
<div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
<?php while ($r = $lain->fetch_assoc()): ?><div class="col"><?= kartu($r, 'produk_detail.php?id=' . $id) ?></div><?php endwhile; ?></div>
<?php endif; include 'includes/footer.php'; ?>
