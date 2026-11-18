<?php
/**
 * Plugin Name: Batas Buku Tamu Harian
 * Plugin URI: https://github.com/tutorialslims/plugin-visitor_daily_limit
 * Description: Buku tamu (index.php?p=visitor) hanya bisa diisi satu kali per hari: anggota terdaftar berdasarkan ID anggota, tamu non-anggota berdasarkan Nama Tamu + Institusi yang sama.
 * Version: 1.2.1
 * Author: Tutorial SLiMS
 * Author URI: https://instagram.com/tutorial_slims
 */

// Logika plugin di bawah ini dipadatkan & dienkripsi (sodium secretbox) agar
// tidak diubah sembarangan. Didekripsi otomatis saat SLiMS memuat berkas ini;
// tidak memerlukan ekstensi PHP tambahan (sodium bawaan PHP 7.2+).
if (!function_exists('sodium_crypto_secretbox_open')) {
    trigger_error('Plugin Batas Buku Tamu Harian membutuhkan ekstensi PHP sodium (PHP 7.2 ke atas).', E_USER_WARNING);
    return;
}
$__vdl_k = base64_decode(
"rBVg4A8gK7nsOnsV1FID0BR0543heqxef9qx0uLid6Y=
");
$__vdl_n = base64_decode(
"lszJoC14mbtj4aqi4ZgWzSwRflt/hOlm
");
$__vdl_c = base64_decode(
"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
");
$__vdl_p = @sodium_crypto_secretbox_open($__vdl_c, $__vdl_n, $__vdl_k);
if ($__vdl_p === false) {
    trigger_error('Plugin Batas Buku Tamu Harian: berkas rusak atau telah diubah, plugin tidak dimuat.', E_USER_WARNING);
    unset($__vdl_k, $__vdl_n, $__vdl_c);
    return;
}
eval(gzinflate($__vdl_p));
unset($__vdl_k, $__vdl_n, $__vdl_c, $__vdl_p);
