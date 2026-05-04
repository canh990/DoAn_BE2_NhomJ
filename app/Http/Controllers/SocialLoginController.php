<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')
                ->stateless()
                ->user();

            $user = $this->findOrCreateSocialUser('google', $socialUser);

            Auth::login($user, true);

            return redirect()->route('home');
        } catch (\Exception $e) {
            Log::error('Google Login Error', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return redirect()->route('login')
                ->with('error', __('auth.social_login_error', ['provider' => 'Google', 'message' => $e->getMessage()]));
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->scopes(['email'])
            ->fields(['id', 'name', 'email'])
            ->stateless()
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')
                ->scopes(['email'])
                ->fields(['id', 'name', 'email'])
                ->stateless()
                ->user();

            $user = $this->findOrCreateSocialUser('facebook', $socialUser);

            Auth::login($user, true);

            return redirect()->route('home');
        } catch (\Exception $e) {
            Log::error('Facebook Login Error', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return redirect()->route('login')
                ->with('error', __('auth.social_login_error', ['provider' => 'Facebook', 'message' => $e->getMessage()]));
        }
    }

    private function findOrCreateSocialUser(string $provider, SocialiteUser $socialUser): User
    {
        $oauthId = (string) $socialUser->getId();
        $email = $socialUser->getEmail() ?: $oauthId . '@' . $provider . '.local';
        $tenDangNhap = $this->generateUniqueUsername(
            $socialUser->getNickname()
            ?? $socialUser->getName()
            ?? Str::before($email, '@')
            ?? $provider . '_' . $oauthId
        );

        $user = User::query()
            ->where(function ($query) use ($provider, $oauthId) {
                $query->where('nha_cung_cap_oauth', $provider)
                    ->where('id_oauth', $oauthId);
            })
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            return User::create([
                'ten_dang_nhap' => $tenDangNhap,
                'email' => $email,
                'mat_khau_hash' => \Illuminate\Support\Facades\Hash::make(Str::random(40)), // Ensure password is always hashed
                'da_xac_thuc' => true,
                'nha_cung_cap_oauth' => $provider,
                'id_oauth' => $oauthId,
            ]);
        }

        $updates = [];

        if (blank($user->nha_cung_cap_oauth)) {
            $updates['nha_cung_cap_oauth'] = $provider;
        }

        if (blank($user->id_oauth)) {
            $updates['id_oauth'] = $oauthId;
        }

        if (blank($user->ten_dang_nhap)) {
            $updates['ten_dang_nhap'] = $tenDangNhap;
        }

        if (blank($user->email)) {
            $updates['email'] = $email;
        }

        if (! empty($updates)) {
            $user->update($updates);
        }

        return $user;
    }

    private function generateUniqueUsername(string $source): string
    {
        $base = Str::of($source)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_')
            ->value();

        $base = $base !== '' ? $base : 'nguoi_dung';
        $username = Str::limit($base, 45, '');
        $counter = 1;

        while (User::where('ten_dang_nhap', $username)->exists()) {
            $suffix = '_' . $counter;
            $username = Str::limit($base, 50 - strlen($suffix), '') . $suffix;
            $counter++;
        }

        return $username;
    }
}