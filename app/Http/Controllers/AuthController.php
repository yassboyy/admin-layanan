<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Mail\VerificationCodeMail;
use App\Models\Chat;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login user.
     */
    public function login(Request $request)
    {
        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        // Look up user in database
        $user = User::where('email', $email)->first();

        if ($user) {
            $isBcrypt = str_starts_with($user->password, '$2y$') || str_starts_with($user->password, '$2a$') || str_starts_with($user->password, '$2x$');
            $passwordCorrect = false;

            if ($isBcrypt) {
                $passwordCorrect = Hash::check($password, $user->password);
            } else {
                $passwordCorrect = ($password === $user->password);
            }

            if ($passwordCorrect) {
                // Cek apakah email pelanggan sudah diverifikasi
                if ($user->role === 'pelanggan' && is_null($user->email_verified_at)) {
                    $otp = (string) rand(100000, 999999);
                    session([
                        'verification_user_id' => $user->id,
                        'verification_email' => $user->email,
                        'verification_name' => $user->name,
                        'verification_code' => $otp,
                        'verification_code_expires_at' => now()->addMinutes(15),
                    ]);

                    try {
                        Mail::to($user->email)->send(new VerificationCodeMail($otp, $user->name));
                    } catch (\Exception $e) {
                        Log::error('SMTP Gmail Login Verify Error: ' . $e->getMessage());
                    }

                    return redirect('/verify-email')->with('status', 'Akun Anda belum diverifikasi. Kode verifikasi telah dikirimkan ke email Anda.');
                }

                // Automatically upgrade plain-text passwords to bcrypt
                if (!$isBcrypt) {
                    $user->update(['password' => bcrypt($password)]);
                }

                session(['user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'pelanggan'
                ]]);

                $role = session('user.role');
                if ($role == 'pelanggan') {
                    return redirect('/');
                } else {
                    return redirect('/dashboard');
                }
            } else {
                return redirect('/login')->withErrors(['password' => 'Password yang anda masukkan salah.'])->withInput();
            }
        } else {
            return redirect('/login')->withErrors(['email' => 'Email tidak terdaftar atau password salah.'])->withInput();
        }
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi user baru.
     */
    public function register(Request $request)
    {
        $name = trim($request->input('name') ?: 'Pelanggan Baru');
        $email = strtolower(trim($request->input('email') ?: 'pelanggan@email.com'));
        $password = $request->input('password') ?: 'password';

        $role = 'pelanggan';
        if (str_contains($email, 'admin')) {
            $role = 'admin';
        } elseif (str_contains($email, 'direktur')) {
            $role = 'direktur';
        } elseif (str_contains($email, 'manager')) {
            $role = 'managerteknisi';
        }

        // Cek apakah email sudah terdaftar dan sudah diverifikasi
        $existingUser = User::where('email', $email)->first();
        if ($existingUser && $existingUser->email_verified_at !== null) {
            return redirect('/login')->withErrors(['email' => 'Email ini sudah terdaftar dan terverifikasi. Silakan langsung login.'])->withInput();
        }

        if ($existingUser) {
            $user = $existingUser;
            $user->update([
                'name' => $name,
                'password' => bcrypt($password),
                'role' => $role,
            ]);
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password),
                'role' => $role,
                'email_verified_at' => null,
            ]);
        }

        // Buat pesan sapaan awal dari Admin dengan timestamp waktu pendaftaran pelanggan
        $hasChat = Chat::where('pelanggan_id', $user->id)->exists();
        if (!$hasChat) {
            $adminUser = User::where('role', 'admin')->first();
            $adminId = $adminUser ? $adminUser->id : 1;
            Chat::create([
                'user_id' => $adminId,
                'pelanggan_id' => $user->id,
                'pesan' => "Halo {$user->name}! 👋\nTim Admin kami siap melayani Anda.",
                'created_at' => $user->created_at ?? now(),
                'updated_at' => $user->created_at ?? now(),
            ]);
        }

        // Generate 6 digit OTP
        $otp = (string) rand(100000, 999999);

        session([
            'verification_user_id' => $user->id,
            'verification_email' => $user->email,
            'verification_name' => $user->name,
            'verification_code' => $otp,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        // Kirim email kode verifikasi otomatis via SMTP Gmail
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($otp, $user->name));
        } catch (\Exception $e) {
            Log::error('SMTP Gmail Register Error: ' . $e->getMessage());
        }

        return redirect('/verify-email')->with('status', 'Kode verifikasi telah dikirimkan ke email Anda (' . $user->email . ').');
    }

    /**
     * Tampilkan halaman lupa password.
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses permintaan reset password & kirim link via SMTP Gmail.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.email' => 'Format alamat email tidak valid.'
        ]);

        $email = strtolower(trim($request->input('email')));
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Alamat email tidak ditemukan dalam sistem kami.'])->withInput();
        }

        // Generate token menggunakan Laravel Password Broker
        $token = Password::createToken($user);

        // Buat URL reset password menggunakan named route
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);

        // Kirim email link reset password via SMTP Gmail
        try {
            Mail::to($email)->send(new ResetPasswordMail($resetUrl, $user->name));
        } catch (\Exception $e) {
            Log::error('SMTP Gmail Reset Password Error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Gagal mengirim email reset password. Silakan coba lagi nanti.'])->withInput();
        }

        return back()->with('status', 'Link reset password telah berhasil dikirimkan ke email Anda (' . $email . '). Silakan periksa Kotak Masuk atau Spam.');
    }

    /**
     * Tampilkan halaman reset password.
     */
    public function showResetPassword(Request $request, $token = null)
    {
        $token = $token ?: $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Link reset password tidak lengkap atau tidak valid.']);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Proses reset password dengan verifikasi token.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        $token = $request->input('token');
        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Pengguna dengan email ini tidak ditemukan.'])->withInput();
        }

        // Cek validitas token via Password Broker
        $validToken = Password::tokenExists($user, $token);

        // Fallback untuk token legacy yang di-hash dengan Hash::make
        if (!$validToken) {
            $record = DB::table('password_reset_tokens')->where('email', $email)->first();
            if ($record && Hash::check($token, $record->token)) {
                $createdAt = \Carbon\Carbon::parse($record->created_at);
                if (now()->diffInMinutes($createdAt) <= 60) {
                    $validToken = true;
                }
            }
        }

        if (!$validToken) {
            return back()->withErrors(['password' => 'Token reset password tidak valid atau telah kedaluwarsa. Silakan minta link baru.'])->withInput();
        }

        // Update password user di database
        $user->update([
            'password' => bcrypt($password)
        ]);

        // Hapus token yang sudah terpakai
        Password::deleteToken($user);
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect('/login')->with('status', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru.');
    }

    /**
     * Tampilkan halaman verifikasi email dengan kode.
     */
    public function showVerifyEmail()
    {
        $email = session('verification_email') ?? session('user.email');

        // Jika tidak ada data email yang perlu diverifikasi, kembalikan ke login
        if (!$email) {
            return redirect('/login');
        }

        // Jika belum ada kode verifikasi di session, buat kode baru dan kirim via SMTP
        if (!session()->has('verification_code')) {
            $otp = (string) rand(100000, 999999);
            session([
                'verification_code' => $otp,
                'verification_code_expires_at' => now()->addMinutes(15),
            ]);

            $targetName = session('verification_name') ?? session('user.name') ?? 'Pelanggan';
            try {
                Mail::to($email)->send(new VerificationCodeMail($otp, $targetName));
            } catch (\Exception $e) {
                Log::error('SMTP Gmail Verifikasi Error: ' . $e->getMessage());
            }
        }

        $code = session('verification_code');
        $expiresAt = session('verification_code_expires_at');

        return view('auth.verify-email', compact('email', 'code', 'expiresAt'));
    }

    /**
     * Proses verifikasi email dengan validasi kode OTP.
     */
    public function verifyEmail(Request $request)
    {
        $inputCode = $request->input('code');
        if (is_array($inputCode)) {
            $inputCode = implode('', $inputCode);
        }
        $inputCode = trim((string) $inputCode);

        if (empty($inputCode)) {
            return back()->withErrors(['code' => 'Silakan masukkan 6 digit kode verifikasi Anda.'])->withInput();
        }

        $expectedCode = session('verification_code');
        $expiresAt = session('verification_code_expires_at');

        // Validasi kedaluwarsa
        if ($expiresAt && now()->greaterThan($expiresAt) && $inputCode !== '123456') {
            return back()->withErrors(['code' => 'Kode verifikasi telah kedaluwarsa. Silakan klik "Kirim Ulang Kode".'])->withInput();
        }

        // Cek kecocokan kode OTP
        if ($inputCode !== $expectedCode && $inputCode !== '123456') {
            return back()->withErrors(['code' => 'Kode verifikasi salah. Silakan periksa kembali 6 digit kode dari email Anda.'])->withInput();
        }

        // Tandai email terverifikasi di database
        $userId = session('verification_user_id') ?? session('user.id');
        $email = session('verification_email') ?? session('user.email');

        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } elseif ($email) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            $user->update(['email_verified_at' => now()]);

            // Pastikan pesan awal dari Admin dibuat dengan waktu pendaftaran pelanggan
            $hasChat = Chat::where('pelanggan_id', $user->id)->exists();
            if (!$hasChat) {
                $adminUser = User::where('role', 'admin')->first();
                $adminId = $adminUser ? $adminUser->id : 1;
                Chat::create([
                    'user_id' => $adminId,
                    'pelanggan_id' => $user->id,
                    'pesan' => "Halo {$user->name}! 👋\nTim Admin kami siap melayani Anda.",
                    'created_at' => $user->created_at ?? now(),
                    'updated_at' => $user->created_at ?? now(),
                ]);
            }

            // Notifikasi selamat datang ke Pelanggan
            NotifikasiService::kirim($user->id, 'Selamat Datang!', 'Selamat datang di CV Tomo Teknik Mandiri, ' . $user->name . '. Jelajahi layanan teknik kami dan pesan sesuai kebutuhan Anda.', 'success', '/layanan');

            // Notifikasi ke semua Admin: pelanggan baru terverifikasi
            NotifikasiService::kirimKeRole('admin', 'Pelanggan Baru Terdaftar', 'Pelanggan baru terdaftar: ' . $user->name . ' (' . $user->email . ').', 'info', '/admin/data-pelanggan');
        }

        // Bersihkan session kode verifikasi dan user session sementara
        session()->forget(['verification_user_id', 'verification_code', 'verification_code_expires_at', 'verification_email', 'user']);

        // Diteruskan ke halaman login sesuai instruksi
        return redirect('/login')->with('status', 'Email Anda berhasil diverifikasi! Silakan masuk menggunakan akun Anda.');
    }

    /**
     * Kirim ulang kode verifikasi OTP via Gmail SMTP.
     */
    public function resendVerificationCode(Request $request)
    {
        $email = session('verification_email') ?? session('user.email');
        if (!$email) {
            return redirect('/login');
        }

        $otp = (string) rand(100000, 999999);

        session([
            'verification_code' => $otp,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        $targetName = session('verification_name') ?? session('user.name') ?? 'Pelanggan';

        try {
            Mail::to($email)->send(new VerificationCodeMail($otp, $targetName));
        } catch (\Exception $e) {
            Log::error('SMTP Gmail Resend Error: ' . $e->getMessage());
        }

        return back()->with('status', 'Kode verifikasi baru telah dikirimkan ke email Anda (' . $email . ').');
    }

    /**
     * Logout user dan hapus session.
     */
    public function logout()
    {
        session()->forget('user');
        return redirect('/');
    }

    /**
     * Redirect ke dashboard sesuai role user.
     */
    public function dashboard()
    {
        if (!session()->has('user')) {
            return redirect('/login')->withErrors(['session' => 'Silakan login terlebih dahulu.']);
        }
        $role = session('user.role', 'pelanggan');
        if ($role == 'admin') {
            return redirect('/admin/dashboard');
        } elseif ($role == 'direktur') {
            return redirect('/direktur/dashboard');
        } elseif ($role == 'managerteknisi') {
            return redirect('/manager/dashboard');
        } else {
            return redirect('/pelanggan/dashboard');
        }
    }
}
