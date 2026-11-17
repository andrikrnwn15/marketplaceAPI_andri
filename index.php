<?php require 'config/koneksi.php'; include 'includes/header.php';
$q = trim($_GET['q'] ?? ''); $k = (int)($_GET['k'] ?? 0); $s = $_GET['s'] ?? 'baru';
$order = ['baru'=>'p.id_produk DESC', 'murah'=>'p.harga ASC', 'mahal'=>'p.harga DESC'][$s] ?? 'p.id_produk DESC';
$like = "%$q%";
$st = $conn->prepare("SELECT p.*, s.nama_toko FROM produk p JOIN penjual s ON p.id_penjual=s.id_penjual
  WHERE p.nama_produk LIKE ? AND (? = 0 OR p.id_kategori = ?) ORDER BY $order");
$st->bind_param('sii', $like, $k, $k); $st->execute(); $res = $st->get_result();
$kats = $conn->query("SELECT * FROM kategori ORDER BY id_kategori");
$back = ltrim($_SERVER['REQUEST_URI'] ?? '', '/') ?: 'index.php';
$qs = function ($ka, $sa) use ($q) { return 'index.php?' . http_build_query(['q' => $q, 'k' => $ka, 's' => $sa]); };
?>
<?php if ($q === '' && !$k): ?>
<div class="hero"><h1>Belanja Kebutuhan Pasar,<br>Langsung dari Penjual Lokal</h1>
  <p class="mb-0 opacity-75">Sayur, buah, kerajinan, dan makanan olahan khas Bengkulu dalam satu tempat.</p></div>
<?php endif; ?>
<div class="mb-2">
  <a class="chip <?= !$k ? 'on' : '' ?>" href="<?= e($qs(0, $s)) ?>">Semua</a>
  <?php while ($c = $kats->fetch_assoc()): ?>
    <a class="chip <?= $k == $c['id_kategori'] ? 'on' : '' ?>" href="<?= e($qs($c['id_kategori'], $s)) ?>"><?= e($c['nama_kategori']) ?></a>
  <?php endwhile; ?>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="sect mb-0"><?= $q !== '' ? 'Hasil untuk "' . e($q) . '"' : 'Rekomendasi untuk Anda' ?> <small class="text-muted fs-6">(<?= $res->num_rows ?> produk)</small></div>
  <div>
    <?php foreach (['baru' => 'Terbaru', 'murah' => 'Termurah', 'mahal' => 'Termahal'] as $v => $l): ?>
      <a class="chip <?= $s === $v ? 'on' : '' ?>" href="<?= e($qs($k, $v)) ?>"><?= $l ?></a>
    <?php endforeach; ?>
  </div>
</div>
<?php if (!$res->num_rows): ?><div class="box text-center py-5">😕 Produk tidak ditemukan. <a href="index.php">Lihat semua produk</a></div><?php endif; ?>
<div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
<?php while ($r = $res->fetch_assoc()): ?><div class="col"><?= kartu($r, $back) ?></div><?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>
