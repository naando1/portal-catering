@extends('layouts.customer')

@section('content')
    <div class="auth-header">
        <h3>Buat Akun</h3>
        <p class="text-muted">Mulai dengan <a href="/" class="portal-link primary-color">Katering Kita</a></p>
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="input-group">
                <span class="input-icon">
                    <i class="fas fa-user"></i>
                </span>
                <input id="name" class="form-control input-with-icon block w-full" 
                       type="text" name="name" value="{{ old('name') }}" 
                       required autofocus autocomplete="name" placeholder="Nama Lengkap">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="input-group">
                <span class="input-icon">
                    <i class="fas fa-envelope"></i>
                </span>
                <input id="email" class="form-control input-with-icon block w-full" 
                       type="email" name="email" value="{{ old('email') }}" 
                       required autocomplete="username" placeholder="Alamat Email">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="input-group">
                <span class="input-icon">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="password" class="form-control input-with-icon block w-full"
                       type="password" name="password" required autocomplete="new-password" 
                       placeholder="Kata Sandi">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="input-group">
                <span class="input-icon">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="password_confirmation" class="form-control input-with-icon block w-full"
                       type="password" name="password_confirmation" required autocomplete="new-password" 
                       placeholder="Konfirmasi Kata Sandi">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="btn-primary w-full">
                {{ __('Buat Akun') }}
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-sm text-gray-600">
                {{ __('Sudah punya akun?') }} 
                <a href="{{ route('login') }}" class="auth-link">{{ __('Masuk') }}</a>
            </p>
        </div>
    </div>
@endsection
