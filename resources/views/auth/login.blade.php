@extends('layouts.customer')

@section('content')
    <div class="auth-header">
        <h3>Selamat Datang Kembali!</h3>
        <p class="text-muted">Masuk untuk melanjutkan ke <a href="/" class="portal-link primary-color">Katering Kita</a></p>
    </div>

    <div class="auth-body">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="input-group">
                <span class="input-icon">
                    <i class="fas fa-envelope"></i>
                </span>
                <input id="email" class="form-control input-with-icon block w-full" type="email" name="email" 
                       value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Alamat Email">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="input-group">
                <span class="input-icon">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="password" class="form-control input-with-icon block w-full" 
                       type="password" name="password" required autocomplete="current-password" placeholder="Kata Sandi">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between mb-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 checkbox-custom" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                </label>
                
                @if (Route::has('password.request'))
                    <a class="auth-link text-sm" href="{{ route('password.request') }}">
                        {{ __('Lupa kata sandi?') }}
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full">
                {{ __('Masuk') }}
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-sm text-gray-600">
                {{ __('Belum punya akun?') }} 
                <a href="{{ route('register') }}" class="auth-link">{{ __('Daftar') }}</a>
            </p>
        </div>
    </div>
@endsection
