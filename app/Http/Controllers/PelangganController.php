<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Pemesanan;
use App\Models\User;
use App\Services\NotifikasiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PelangganController extends Controller
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
     * Dashboard Pelanggan.
     */
    public function dashboard()
    {
        $orders = Pemesanan::where('user_id', $this->getUserId())
            ->with('pemesananLayanan')
            ->latest()
            ->get();
        return view('Pelanggan.dashboard-pelanggan', compact('orders'));
    }

    /**
     * Daftar pesanan pelanggan dengan filter pencarian menyeluruh.
     */
    public function pesanan(Request $request)
    {
        $query = Pemesanan::where('user_id', $this->getUserId())
            ->with('pemesananLayanan.layanan');

        // Filter Search Menyeluruh (ID Pesanan, Layanan, Total Harga, Status)
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $cleanNumeric = preg_replace('/[^0-9]/', '', $search);

            $query->where(function ($q) use ($search, $cleanNumeric) {
                $q->where('id_pesanan', 'like', "%{$search}%")
                  ->orWhereHas('pemesananLayanan', function ($lq) use ($search) {
                      $lq->where('nama_layanan', 'like', "%{$search}%")
                         ->orWhereHas('layanan', function ($mlq) use ($search) {
                             $mlq->where('nama_layanan', 'like', "%{$search}%");
                         });
                  });

                if (!empty($cleanNumeric)) {
                    $q->orWhere('total_harga', 'like', "%{$cleanNumeric}%");
                }

                // Match status text
                $searchLower = strtolower($search);
                if (str_contains($searchLower, 'pemesanan')) {
                    $q->orWhere('status_tipe', 1);
                }
                if (str_contains($searchLower, 'pengerjaan')) {
                    $q->orWhere('status_tipe', 2);
                }
                if (str_contains($searchLower, 'pelunasan') || str_contains($searchLower, 'menunggu')) {
                    $q->orWhere('status_tipe', 3);
                }
                if (str_contains($searchLower, 'selesai')) {
                    $q->orWhere('status_tipe', 4);
                }
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status_tipe', $request->input('status'));
        }

        // Filter Tanggal Pesan (Date Range / Single Date)
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->input('tanggal_dari'));
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->input('tanggal_sampai'));
        }
        if ($request->filled('tanggal_pesan')) {
            $query->whereDate('created_at', $request->input('tanggal_pesan'));
        } elseif ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', $request->input('tanggal_mulai'));
        }

        // Sort By
        $sort = $request->input('sort', 'terbaru');
        switch ($sort) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'harga_tertinggi':
                $query->orderByDesc('total_harga');
                break;
            case 'harga_terendah':
                $query->orderBy('total_harga');
                break;
            case 'id_asc':
                $query->orderBy('id_pesanan');
                break;
            case 'id_desc':
                $query->orderByDesc('id_pesanan');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $orders = $query->get();

        return view('Pelanggan.pesanan-pelanggan', compact('orders', 'sort'));
    }

    /**
     * Detail pesanan pelanggan.
     */
    public function detailPesanan($id)
    {
        $order = Pemesanan::where('user_id', $this->getUserId())
            ->where('id', $id)
            ->with(['pemesananLayanan.layanan.dokumenLayanan', 'pemesananDokumen', 'validasiDokumen', 'pelaporanTeknisi'])
            ->firstOrFail();
        return view('Pelanggan.detail-pesanan-pelanggan', compact('order'));
    }

    /**
     * Halaman chat pelanggan ke admin.
     */
    public function chat()
    {
        $pelangganId = $this->getUserId();
        
        // Pastikan ada pesan sapaan awal dari Admin jika belum ada percakapan
        $hasChat = Chat::where('pelanggan_id', $pelangganId)->exists();
        if (!$hasChat && $pelangganId) {
            $adminUser = User::where('role', 'admin')->first();
            $adminId = $adminUser ? $adminUser->id : 1;
            $pelanggan = User::find($pelangganId);
            $pelangganName = $pelanggan ? $pelanggan->name : session('user.name', 'Pelanggan');

            Chat::create([
                'user_id' => $adminId,
                'pelanggan_id' => $pelangganId,
                'pesan' => "Halo {$pelangganName}! 👋\nTim Admin kami siap melayani Anda.",
                'created_at' => $pelanggan ? ($pelanggan->created_at ?? now()) : now(),
                'updated_at' => $pelanggan ? ($pelanggan->created_at ?? now()) : now(),
            ]);
        }

        $chats = Chat::where('pelanggan_id', $pelangganId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('Pelanggan.chat-pelanggan', compact('chats'));
    }

    /**
     * Kirim pesan dari pelanggan ke admin.
     */
    public function sendChat(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string|max:3000',
        ]);

        $pelangganId = $this->getUserId();

        $chat = Chat::create([
            'user_id' => $pelangganId,
            'pelanggan_id' => $pelangganId,
            'pesan' => trim($request->input('pesan')),
        ]);

        // Notifikasi ke semua Admin: ada pesan chat baru dari pelanggan
        $senderName = session('user.name', 'Pelanggan');
        NotifikasiService::kirimKeRole('admin', 'Pesan Chat Baru', 'Pesan baru dari ' . $senderName . ': "' . Str::limit($chat->pesan, 50) . '"', 'info', '/admin/chat?pelanggan_id=' . $pelangganId);

        if ($request->expectsJson() || $request->ajax()) {
            $created = Carbon::parse($chat->created_at);
            return response()->json([
                'success' => true,
                'chat' => [
                    'id' => $chat->id,
                    'pesan' => $chat->pesan,
                    'sender_id' => $chat->user_id,
                    'sender_name' => session('user.name', 'Saya'),
                    'is_me' => true,
                    'time' => $created->format('H:i'),
                    'date' => $created->format('d M Y'),
                ],
            ]);
        }

        return redirect('/pelanggan/chat');
    }

    /**
     * Dapatkan daftar pesan (AJAX polling) untuk pelanggan.
     */
    public function getChatMessages()
    {
        $pelangganId = $this->getUserId();

        $hasChat = Chat::where('pelanggan_id', $pelangganId)->exists();
        if (!$hasChat && $pelangganId) {
            $adminUser = User::where('role', 'admin')->first();
            $adminId = $adminUser ? $adminUser->id : 1;
            $pelanggan = User::find($pelangganId);
            $pelangganName = $pelanggan ? $pelanggan->name : session('user.name', 'Pelanggan');

            Chat::create([
                'user_id' => $adminId,
                'pelanggan_id' => $pelangganId,
                'pesan' => "Halo {$pelangganName}! 👋\nTim Admin kami siap melayani Anda.",
                'created_at' => $pelanggan ? ($pelanggan->created_at ?? now()) : now(),
                'updated_at' => $pelanggan ? ($pelanggan->created_at ?? now()) : now(),
            ]);
        }

        $chats = Chat::where('pelanggan_id', $pelangganId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = [];
        foreach ($chats as $c) {
            $created = Carbon::parse($c->created_at);
            $formatted[] = [
                'id' => $c->id,
                'pesan' => $c->pesan,
                'sender_id' => $c->user_id,
                'sender_name' => ($c->user && $c->user->role === 'admin') ? 'Admin Support' : ($c->user ? $c->user->name : 'Saya'),
                'is_me' => ($c->user_id == $pelangganId),
                'time' => $created->format('H:i'),
                'date' => $created->format('d M Y'),
            ];
        }

        return response()->json([
            'success' => true,
            'messages' => $formatted,
        ]);
    }
}
