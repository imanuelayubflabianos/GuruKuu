<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

class MaintenanceService
{
    /**
     * Cek apakah mode pemeliharaan (maintenance) siswa sedang aktif.
     */
    public static function isSiswaMaintenanceActive(): bool
    {
        $enabled = Setting::get('maintenance_siswa_enabled', '0');
        if ($enabled !== '1') {
            return false;
        }

        $type = Setting::get('maintenance_siswa_type', 'manual');
        if ($type === 'manual') {
            return true;
        }

        if ($type === 'scheduled') {
            $startStr = Setting::get('maintenance_siswa_start');
            $endStr = Setting::get('maintenance_siswa_end');

            if (!$startStr && !$endStr) {
                return true;
            }

            $now = Carbon::now();
            $start = $startStr ? Carbon::parse($startStr) : null;
            $end = $endStr ? Carbon::parse($endStr) : null;

            if ($start && $end) {
                return $now->between($start, $end);
            }
            if ($start && !$end) {
                return $now->gte($start);
            }
            if (!$start && $end) {
                return $now->lte($end);
            }
        }

        return false;
    }

    /**
     * Dapatkan informasi lengkap status pemeliharaan siswa.
     */
    public static function getSiswaMaintenanceInfo(): array
    {
        $enabled = Setting::get('maintenance_siswa_enabled', '0') === '1';
        $isActive = self::isSiswaMaintenanceActive();
        $type = Setting::get('maintenance_siswa_type', 'manual');
        $startStr = Setting::get('maintenance_siswa_start');
        $endStr = Setting::get('maintenance_siswa_end');
        $customMessage = Setting::get('maintenance_siswa_message', '');

        $startFormatted = $startStr ? Carbon::parse($startStr)->locale('id')->translatedFormat('d M Y, H:i') : null;
        $endFormatted = $endStr ? Carbon::parse($endStr)->locale('id')->translatedFormat('d M Y, H:i') : null;

        if ($type === 'scheduled' && $startFormatted && $endFormatted) {
            $scheduleText = "Pemeliharaan terjadwal dari {$startFormatted} WIB hingga {$endFormatted} WIB.";
        } elseif ($type === 'scheduled' && $endFormatted) {
            $scheduleText = "Pemeliharaan terjadwal selesai pada {$endFormatted} WIB.";
        } elseif ($type === 'scheduled' && $startFormatted) {
            $scheduleText = "Pemeliharaan terjadwal dimulai pada {$startFormatted} WIB.";
        } else {
            $scheduleText = "Pemeliharaan aktif hingga dinonaktifkan kembali oleh Administrator.";
        }

        $defaultMessage = "Sistem saat ini sedang dalam mode pemeliharaan (maintenance). Anda tetap dapat melihat dashboard, daftar guru, dan ulasan, namun seluruh aksi pemberian nilai dan pengiriman pesan dinonaktifkan sementara.";

        return [
            'enabled' => $enabled,
            'is_active' => $isActive,
            'type' => $type,
            'start' => $startStr,
            'end' => $endStr,
            'start_formatted' => $startFormatted,
            'end_formatted' => $endFormatted,
            'schedule_text' => $scheduleText,
            'message' => !empty($customMessage) ? $customMessage : $defaultMessage,
        ];
    }
}
