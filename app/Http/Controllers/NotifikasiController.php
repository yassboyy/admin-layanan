<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Helper: Dapatkan user_id dari session.
     */
    private function getUserId()
    {
        if (!session()->has('user')) {
            return null;
        }
        if (empty(session('user.id')) && session()->has('user.email')) {
            $user = \App\Models\User::where('email', session('user.email'))->first();
            if ($user) {
                $userSession = session('user');
                $userSession['id'] = $user->id;
                session(['user' => $userSession]);
                return $user->id;
            }
        }
        return session('user.id');
    }

    /**
     * Ambil data notifikasi untuk user yang sedang login (JSON).
     */
    public function getData()
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return response()->json(['notifikasi' => [], 'unread_count' => 0]);
        }

        $notifikasi = Notifikasi::where('user_id', $userId)
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($n) {
                $created = \Carbon\Carbon::parse($n->created_at);
                $now = \Carbon\Carbon::now();
                $diff = $created->diffForHumans($now, true);

                return [
                    'id'      => $n->id,
                    'judul'   => $n->judul,
                    'pesan'   => $n->pesan,
                    'icon'    => $n->icon,
                    'url'     => $n->url,
                    'is_read' => $n->is_read,
                    'waktu'   => $diff . ' yang lalu',
                ];
            });

        $unreadCount = Notifikasi::where('user_id', $userId)->unread()->count();

        return response()->json([
            'notifikasi'   => $notifikasi,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function tandaiBaca($id)
    {
        $userId = $this->getUserId();
        $notif = Notifikasi::where('id', $id)->where('user_id', $userId)->first();

        if ($notif) {
            $notif->update(['is_read' => true]);

            if ($notif->url) {
                return response()->json(['success' => true, 'redirect' => $notif->url]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function tandaiBacaSemua()
    {
        $userId = $this->getUserId();
        if ($userId) {
            Notifikasi::where('user_id', $userId)->unread()->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
