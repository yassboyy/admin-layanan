<?php

namespace App\Jobs;

use App\Mail\NewOrderNotification;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Pemesanan $order;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Pemesanan $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = config('mail.admin_notification', config('mail.from.address'));

        if (empty($adminEmail) || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Email admin_notification belum dikonfigurasi atau tidak valid untuk notifikasi pesanan #' . $this->order->id_pesanan);
            return;
        }

        $this->order->loadMissing('pemesananLayanan.layanan');

        Mail::to($adminEmail)->send(new NewOrderNotification($this->order));

        Log::info('Email notifikasi pesanan baru #' . $this->order->id_pesanan . ' berhasil dikirim ke admin: ' . $adminEmail);
    }
}
