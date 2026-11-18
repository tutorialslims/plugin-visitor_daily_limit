<?php
/**
 * Plugin Name: Batas Buku Tamu Harian
 * Plugin URI: https://instagram.com/tutorial_slims
 * Description: Buku tamu (index.php?p=visitor) hanya bisa diisi satu kali per hari: anggota terdaftar berdasarkan ID anggota, tamu non-anggota berdasarkan Nama Tamu + Institusi yang sama.
 * Version: 1.1.0
 * Author: Tutorial SLiMS
 * Author URI: https://instagram.com/tutorial_slims
 */

use SLiMS\DB;
use SLiMS\Json;
use SLiMS\Plugins;

/**
 * Hook MEMBER_ON_VISIT dijalankan oleh lib/Visitor.php HANYA untuk anggota
 * terdaftar, tepat SEBELUM data kunjungan disimpan ke tabel visitor_count.
 * Jika anggota sudah tercatat hari ini (sejak pukul 00.00), proses dihentikan
 * dan halaman buku tamu menerima balasan JSON "sudah mengisi hari ini",
 * sehingga tidak ada baris ganda.
 *
 * Tamu non-anggota ditangani hook NON_MEMBER_ON_VISIT di bawah.
 */
Plugins::getInstance()->register('MEMBER_ON_VISIT', function () {
    global $sysconf;

    $memberId = trim((string)($_POST['memberID'] ?? ''));
    if ($memberId === '') return;

    $db = DB::getInstance();
    $stmt = $db->prepare(
        'SELECT vc.checkin_date, m.member_name, m.member_image
           FROM visitor_count vc
           JOIN member m ON m.member_id = vc.member_id
          WHERE vc.member_id = ? AND vc.checkin_date >= CURDATE()
          ORDER BY vc.checkin_date ASC
          LIMIT 1'
    );
    $stmt->execute([$memberId]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$row) return; // belum mengisi hari ini: lanjutkan proses normal SLiMS

    $isId = stripos($sysconf['default_lang'] ?? 'id_ID', 'id') === 0;
    $time = date('H.i', strtotime($row['checkin_date']));
    $name = trim(strip_tags($row['member_name']));
    $message = $isId
        ? sprintf('%s, Anda sudah mengisi buku tamu hari ini pukul %s. Buku tamu hanya dapat diisi satu kali per hari.', $name, $time)
        : sprintf('%s, you already signed the guest book today at %s. Members can sign in once per day.', $name, $time);

    exit(Json::stringify([
        'message'       => $message,
        'image'         => $row['member_image'] ?: 'person.png',
        'new_token'     => \Volnix\CSRF\CSRF::getToken(),
        'status'        => '',
        'already_today' => true,
    ])->withHeader());
});

/**
 * Tamu non-anggota: dibatasi satu kali per hari bila NAMA TAMU dan INSTITUSI
 * sama dengan kunjungan tamu yang sudah tercatat hari ini.
 * Pencocokan tidak membedakan huruf besar/kecil dan mengabaikan spasi berlebih.
 * Nama sama dengan institusi berbeda tetap dianggap tamu yang berbeda.
 */
Plugins::getInstance()->register('NON_MEMBER_ON_VISIT', function ($data = []) {
    global $sysconf;

    $norm = function ($s) {
        $s = preg_replace('/\s+/u', ' ', trim((string)$s));
        return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    };

    $name = trim((string)($_POST['memberID'] ?? ''));
    $inst = trim((string)($_POST['institution'] ?? ''));
    if ($name === '' || $inst === '') return; // institusi kosong sudah ditolak SLiMS

    // kunjungan tamu (bukan anggota) hari ini
    $stmt = DB::getInstance()->prepare(
        'SELECT member_name, institution, checkin_date
           FROM visitor_count
          WHERE (member_id IS NULL OR member_id = \'\') AND checkin_date >= CURDATE()
          ORDER BY checkin_date ASC'
    );
    $stmt->execute();

    $keyName = $norm($name);
    $keyInst = $norm($inst);
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        if ($norm($row['member_name']) !== $keyName || $norm($row['institution']) !== $keyInst) continue;

        $isId = stripos($sysconf['default_lang'] ?? 'id_ID', 'id') === 0;
        $time = date('H.i', strtotime($row['checkin_date']));
        $message = $isId
            ? sprintf('%s (%s), Anda sudah mengisi buku tamu hari ini pukul %s. Buku tamu hanya dapat diisi satu kali per hari.', $row['member_name'], $row['institution'], $time)
            : sprintf('%s (%s), you already signed the guest book today at %s. Guests can sign in once per day.', $row['member_name'], $row['institution'], $time);

        exit(Json::stringify([
            'message'       => $message,
            'image'         => 'non_member.png',
            'new_token'     => \Volnix\CSRF\CSRF::getToken(),
            'status'        => '',
            'already_today' => true,
        ])->withHeader());
    }
});
