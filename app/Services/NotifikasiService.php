<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke satu user.
     */
    public static function kirim(int $userId, string $judul, string $pesan, string $icon = 'info', ?string $url = null): Notifikasi
    {
        return Notifikasi::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'icon'    => $icon,
            'url'     => $url,
        ]);
    }

    /**
     * Kirim notifikasi ke semua user dengan role tertentu.
     */
    public static function kirimKeRole(string $role, string $judul, string $pesan, string $icon = 'info', ?string $url = null): void
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            self::kirim($user->id, $judul, $pesan, $icon, $url);
        }
    }

    /**
     * Kirim notifikasi ke beberapa role sekaligus.
     */
    public static function kirimKeRoles(array $roles, string $judul, string $pesan, string $icon = 'info', ?string $url = null): void
    {
        foreach ($roles as $role) {
            self::kirimKeRole($role, $judul, $pesan, $icon, $url);
        }
    }
}
