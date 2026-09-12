<?php

if (!function_exists('getUserId')) {
    /**
     * Dapatkan user_id dari session.
     * Helper global yang bisa dipanggil dari Blade views dan controllers.
     */
    function getUserId()
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
}
