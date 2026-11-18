# Batas Buku Tamu Harian

Plugin **SLiMS 9.8.0** yang membatasi pengisian buku tamu (`index.php?p=visitor`) menjadi **satu kali per hari**, sehingga hitungan pengunjung tidak terisi ganda.

Dibuat oleh **Tutorial SLiMS**.

## Cara kerja

| Jenis pengunjung | Aturan |
| --- | --- |
| **Anggota terdaftar** | Hanya bisa mengisi satu kali per hari, dihitung per ID anggota sejak pukul 00.00. |
| **Tamu non-anggota** | Dianggap pengisian ganda bila **Nama Tamu dan Institusi sama** dengan tamu yang sudah tercatat hari ini. Huruf besar/kecil dan spasi berlebih diabaikan. |

- Nama yang sama dengan institusi berbeda tetap dianggap tamu yang berbeda.
- Pada pengisian kedua, tidak ada baris baru di tabel `visitor_count`. Pengunjung menerima pesan, misalnya: *"Budi, Anda sudah mengisi buku tamu hari ini pukul 08.15. Buku tamu hanya dapat diisi satu kali per hari."*
- Pesan tampil dalam bahasa Indonesia atau Inggris mengikuti bahasa SLiMS.
- Memakai hook bawaan SLiMS `MEMBER_ON_VISIT` dan `NON_MEMBER_ON_VISIT`. Tidak ada file inti SLiMS yang diubah dan tidak ada tabel baru.

## Kebutuhan

- SLiMS 9.8.0
- Ekstensi PHP **sodium** aktif (bawaan PHP 7.2 ke atas; umumnya sudah aktif tanpa perlu dipasang terpisah)
- Tampilan pesan paling lengkap (kartu peringatan dengan foto) tersedia di template
  [Modern Library](https://github.com/tutorialslims/template-modern_library). Pada template lain, pesan tetap dikirim oleh SLiMS dan ditampilkan menurut template tersebut.

## Tentang kode plugin ini

Mulai versi 1.2.0, logika plugin dipadatkan dan dienkripsi di dalam berkas
`visitor_daily_limit.plugin.php` agar tidak diubah sembarangan. Berkas ini
mendekripsi dirinya sendiri secara otomatis saat dimuat SLiMS, tanpa memerlukan
ekstensi atau perangkat lunak tambahan di luar PHP itu sendiri. Perilaku plugin
tidak berubah dari sisi pengguna.

## Cara pasang

1. Buka halaman [**Releases**](https://github.com/tutorialslims/plugin-visitor_daily_limit/releases/latest) dan unduh berkas ZIP versi terbaru.
2. Ekstrak, lalu salin folder `visitor_daily_limit` ke folder `plugins/` di instalasi SLiMS Anda
   sehingga hasilnya `slims/plugins/visitor_daily_limit/visitor_daily_limit.plugin.php`.
3. Login ke Admin SLiMS, buka **Sistem → Plugin**.
4. Cari **Batas Buku Tamu Harian**, lalu aktifkan.
5. Uji dengan mengisi buku tamu dua kali dengan data yang sama pada hari yang sama.

## Catatan

- Pembatasan dihitung per hari kalender (mulai pukul 00.00 menurut jam server dan database).
- Bila ingin mematikan pembatasan, nonaktifkan plugin di **Sistem → Plugin**.

## Kontak

Apapun masalah error SLiMS, kami siap membantu, silakan DM ke Instagram.

- Email: tutorialslims@gmail.com
- Instagram: [@tutorial_slims](https://instagram.com/tutorial_slims)

## Lisensi

[GPL-3.0](LICENSE), mengikuti lisensi SLiMS.
