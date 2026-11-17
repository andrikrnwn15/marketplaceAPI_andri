<?php
function ikon($r) {
  // [kata kunci pada nama produk => [emoji, warna latar 0-4]]
  $map = [
    'pisang'=>['🍌',1], 'jeruk'=>['🍊',1], 'apel'=>['🍎',2], 'mangga'=>['🥭',1], 'semangka'=>['🍉',2], 'nanas'=>['🍍',1],
    'kelapa'=>['🥥',0], 'durian'=>['🍈',0], 'bayam'=>['🥬',0], 'kangkung'=>['🥬',0], 'sawi'=>['🥬',0], 'sayur'=>['🥬',0],
    'tomat'=>['🍅',2], 'wortel'=>['🥕',1], 'bawang'=>['🧅',1], 'cabai'=>['🌶️',2], 'cabe'=>['🌶️',2], 'jagung'=>['🌽',1],
    'singkong'=>['🍠',1], 'kentang'=>['🥔',1], 'terong'=>['🍆',4], 'kunyit'=>['🌿',1], 'jahe'=>['🌿',1], 'lengkuas'=>['🌿',0],
    'lada'=>['🧂',3], 'merica'=>['🧂',3], 'garam'=>['🧂',3], 'madu'=>['🍯',1], 'telur'=>['🥚',1], 'ikan'=>['🐟',3],
    'ayam'=>['🍗',2], 'beras'=>['🍚',3], 'kopi'=>['☕',2], 'teh'=>['🍵',0], 'pendap'=>['🐟',3], 'kerupuk'=>['🍘',1],
    'kemplang'=>['🍘',1], 'lempuk'=>['🍬',2], 'dodol'=>['🍬',2], 'keripik'=>['🥔',1], 'tempe'=>['🥘',1], 'tahu'=>['🥘',1],
    'tas'=>['👜',1], 'anyaman'=>['🧺',1], 'keranjang'=>['🧺',1], 'selendang'=>['🧣',4], 'batik'=>['👘',4], 'kain'=>['🧵',4],
    'baju'=>['👕',3], 'sandal'=>['🩴',3], 'piring'=>['🍽️',3], 'gelas'=>['🥛',3],
  ];
  $n = strtolower($r['nama_produk'] ?? '');
  foreach ($map as $k => $v) { if (strpos($n, $k) !== false) return $v; }
  $kat = [1=>['🥬',0], 2=>['🧺',1], 3=>['🥘',2], 4=>['🌿',1], 5=>['👘',4]];
  return $kat[(int)($r['id_kategori'] ?? 0)] ?? ['🛍️',3];
}
function thumb($r, $h = 190) {
  global $base; $b = $base ?? '';
  if (!empty($r['gambar'])) return '<img class="thumb" style="height:'.$h.'px" src="'.$b.'uploads/'.e($r['gambar']).'" alt="">';
  [$em, $g] = ikon($r);
  return '<div class="thumb ph g'.$g.'" style="height:'.$h.'px"><span>'.$em.'</span></div>';
}
function kartu($r, $back) {
  $stok = (int)$r['stok'];
  $label = $stok > 0 ? ($stok <= 10 ? '<span class="low">Sisa '.$stok.'</span>' : 'Stok '.$stok) : '<span class="low">Habis</span>';
  $h = '<div class="card-p"><a href="produk_detail.php?id='.$r['id_produk'].'">'.thumb($r).'<div class="p-3">'
     .'<div class="name">'.e($r['nama_produk']).'</div><div class="price">'.rupiah($r['harga']).'</div>'
     .'<div class="meta">📍 '.e($r['nama_toko']).'</div><div class="meta">'.$label.'</div></div></a>';
  if ($stok > 0) {
    $h .= '<form method="post" action="keranjang.php" class="px-3 pb-3"><input type="hidden" name="aksi" value="tambah">'
       .'<input type="hidden" name="id" value="'.$r['id_produk'].'"><input type="hidden" name="back" value="'.e($back).'">'
       .'<button class="btn btn-sm btn-outline-success w-100 fw-bold">+ Keranjang</button></form>';
  }
  return $h.'</div>';
}
function ambilKeranjang($conn) {
  $items = []; $total = 0; $cart = $_SESSION['cart'] ?? [];
  if ($cart) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $res = $conn->query("SELECT p.*, s.nama_toko FROM produk p JOIN penjual s ON p.id_penjual=s.id_penjual WHERE p.id_produk IN ($ids)");
    while ($r = $res->fetch_assoc()) {
      $r['qty'] = min($cart[$r['id_produk']], max(1, $r['stok']));
      $r['sub'] = $r['qty'] * $r['harga']; $total += $r['sub']; $items[] = $r;
    }
  }
  return [$items, $total];
}
