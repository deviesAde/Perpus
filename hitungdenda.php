<?php
function hitungDenda($tgl_kembali, $tgl_seharusnya, $denda_per_hari = 1000) {
    // Ubah string tanggal menjadi objek DateTime
    $tanggal_kembali = new DateTime($tgl_kembali);
    $tanggal_batas = new DateTime($tgl_seharusnya);

    // Hitung selisih hari hanya jika telat
    if ($tanggal_kembali > $tanggal_batas) {
        $selisih = $tanggal_kembali->diff($tanggal_batas)->days;
        return $selisih * $denda_per_hari;
    } else {
        return 0;
    }
}
?>
