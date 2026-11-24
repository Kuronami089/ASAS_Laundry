<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Helper Format Tampilan (Rupiah, Berat kg, dan Tanggal Indonesia)
 */
class FormatHelper
{
    /**
     * Format nominal angka ke mata uang Rupiah (contoh: Rp 12.000).
     */
    public static function rupiah(int|float|string|null $nominal): string
    {
        $angka = (int) round((float) ($nominal ?? 0));
        return "Rp\u{00A0}" . number_format($angka, 0, ',', '.');
    }

    /**
     * Format berat cucian ke kilogram (contoh: 3,5 kg atau 4 kg).
     */
    public static function berat(int|float|string|null $berat): string
    {
        $nilai = (float) ($berat ?? 0);
        $teks  = fmod($nilai, 1.0) === 0.0
            ? number_format($nilai, 0, ',', '.')
            : rtrim(rtrim(number_format($nilai, 2, ',', '.'), '0'), ',');

        return $teks . "\u{00A0}kg";
    }

    /**
     * Format waktu ke teks ringkas bahasa Indonesia (contoh: 24 Nov 2026, 09:30).
     */
    public static function tanggal(CarbonInterface|string|null $datetime): string
    {
        if (!$datetime) {
            return '-';
        }

        $bulan = [
            1  => 'Jan', 2  => 'Feb', 3  => 'Mar', 4  => 'Apr',
            5  => 'Mei', 6  => 'Jun', 7  => 'Jul', 8  => 'Agu',
            9  => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $ts = $datetime instanceof CarbonInterface
            ? $datetime->getTimestamp()
            : strtotime($datetime);

        if ($ts === false) {
            return (string) $datetime;
        }

        $hari  = date('d', $ts);
        $bln   = $bulan[(int) date('n', $ts)] ?? date('M', $ts);
        $tahun = date('Y', $ts);
        $jam   = date('H:i', $ts);

        return "{$hari} {$bln} {$tahun}, {$jam}";
    }
}
