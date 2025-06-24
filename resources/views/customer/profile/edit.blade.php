@extends('layouts.main')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-3">
            @include('customer.partials.sidebar')
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ !request()->query('tab') || request()->query('tab') == 'profile' ? 'active' : '' }}" 
                               href="{{ route('customer.profile.edit') }}">
                                <i class="fas fa-user me-1"></i> Informasi Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->query('tab') == 'health' ? 'active' : '' }}" 
                               href="{{ route('customer.profile.edit', ['tab' => 'health']) }}">
                                <i class="fas fa-heartbeat me-1"></i> Profil Kesehatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->query('tab') == 'password' ? 'active' : '' }}" 
                               href="{{ route('customer.profile.edit', ['tab' => 'password']) }}">
                                <i class="fas fa-lock me-1"></i> Ubah Password
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    {{-- TAB PROFIL PRIBADI --}}
                    @if(!request()->query('tab') || request()->query('tab') == 'profile')
                        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone_number" class="form-label">Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                               id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label">Foto Profil</label>
                                        <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                                               id="profile_picture" name="profile_picture" accept="image/*">
                                        @error('profile_picture')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($user->profile_picture)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                                     alt="Profile Picture" class="img-thumbnail" style="max-width: 100px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Alamat</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </form>
                    @endif
                    
                    {{-- TAB PROFIL KESEHATAN --}}
                    @if(request()->query('tab') == 'health')
                        <form action="{{ route('customer.health-profile.update') }}" method="POST">
                            @csrf
                            
                            {{-- Data Dasar --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">Data Dasar</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="birthdate" class="form-label">Tanggal Lahir *</label>
                                        <input type="date" class="form-control @error('birthdate') is-invalid @enderror" 
                                               id="birthdate" name="birthdate" 
                                               value="{{ old('birthdate', $user->birthdate?->format('Y-m-d')) }}" required>
                                        @error('birthdate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Jenis Kelamin *</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" 
                                                id="gender" name="gender" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="pria" {{ old('gender', $user->gender) == 'pria' ? 'selected' : '' }}>Pria</option>
                                            <option value="wanita" {{ old('gender', $user->gender) == 'wanita' ? 'selected' : '' }}>Wanita</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="height" class="form-label">Tinggi Badan (cm) *</label>
                                        <input type="number" step="0.1" class="form-control @error('height') is-invalid @enderror" 
                                               id="height" name="height" value="{{ old('height', $user->height) }}" 
                                               min="100" max="250" required>
                                        @error('height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="weight" class="form-label">Berat Badan (kg) *</label>
                                        <input type="number" step="0.1" class="form-control @error('weight') is-invalid @enderror" 
                                               id="weight" name="weight" value="{{ old('weight', $user->weight) }}" 
                                               min="30" max="300" required>
                                        @error('weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="activity_level" class="form-label">Tingkat Aktivitas *</label>
                                        <select class="form-select @error('activity_level') is-invalid @enderror" 
                                                id="activity_level" name="activity_level" required>
                                            <option value="">Pilih Tingkat Aktivitas</option>
                                            @foreach($activityLevels as $value => $label)
                                                <option value="{{ $value }}" {{ old('activity_level', $user->activity_level) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('activity_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                {{-- Display BMI jika ada data --}}
                                @if($user->height && $user->weight)
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <strong>BMI Anda:</strong> {{ $user->getBmi() }} - {{ $user->getBmiCategory() }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Kondisi Kesehatan --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">Kondisi Kesehatan</h6>
                                    <p class="text-muted mb-3">Pilih kondisi kesehatan yang Anda miliki untuk mendapatkan rekomendasi menu yang sesuai:</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="has_diabetes" 
                                               name="has_diabetes" value="1" {{ old('has_diabetes', $healthProfile->has_diabetes) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_diabetes">
                                            <strong>Diabetes Mellitus</strong>
                                            <br><small class="text-muted">Sistem akan menyaring menu dengan gula ≤10g, lemak ≤10g, dan menghindari karbohidrat olahan</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="has_hypertension" 
                                               name="has_hypertension" value="1" {{ old('has_hypertension', $healthProfile->has_hypertension) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_hypertension">
                                            <strong>Hipertensi (Tekanan Darah Tinggi)</strong>
                                            <br><small class="text-muted">Sistem akan menyaring menu dengan sodium ≤600mg dan menghindari saus asin</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="has_cholesterol" 
                                               name="has_cholesterol" value="1" {{ old('has_cholesterol', $healthProfile->has_cholesterol) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_cholesterol">
                                            <strong>Kolesterol Tinggi</strong>
                                            <br><small class="text-muted">Sistem akan menyaring menu dengan lemak ≤10g, menghindari gorengan dan jeroan</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="has_heart_disease" 
                                               name="has_heart_disease" value="1" {{ old('has_heart_disease', $healthProfile->has_heart_disease) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_heart_disease">
                                            <strong>Penyakit Jantung</strong>
                                            <br><small class="text-muted">Sistem akan menyaring menu dengan lemak ≤10g, gula ≤10g, sodium ≤600mg</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="has_hemorrhoids" 
                                               name="has_hemorrhoids" value="1" {{ old('has_hemorrhoids', $healthProfile->has_hemorrhoids) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_hemorrhoids">
                                            <strong>Ambeien (Wasir)</strong>
                                            <br><small class="text-muted">Sistem akan menyaring menu dengan serat ≥3g, menghindari pedas dan gorengan</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Preferensi Diet --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">Preferensi Diet</h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="diet_goal" class="form-label">Tujuan Diet *</label>
                                        <select class="form-select @error('diet_goal') is-invalid @enderror" 
                                                id="diet_goal" name="diet_goal" required>
                                            <option value="">Pilih Tujuan Diet</option>
                                            @foreach($dietGoals as $value => $label)
                                                <option value="{{ $value }}" {{ old('diet_goal', $dietPreference->diet_goal) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('diet_goal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="deficit_surplus_percentage" class="form-label">Persentase Defisit/Surplus (10-20%)</label>
                                        <input type="range" class="form-range" id="deficit_surplus_percentage" name="deficit_surplus_percentage" 
                                               min="10" max="20" step="1" value="{{ old('deficit_surplus_percentage', $dietPreference->deficit_surplus_percentage ?? 15) }}">
                                        <div class="d-flex justify-content-between">
                                            <span>10%</span>
                                            <span id="deficit_surplus_value">{{ old('deficit_surplus_percentage', $dietPreference->deficit_surplus_percentage ?? 15) }}%</span>
                                            <span>20%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Alergi Makanan --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">Alergi Makanan</h6>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih bahan makanan yang Anda alergi:</label>
                                        <div class="row">
                                            @php
                                                $selectedAllergies = old('food_allergies', json_decode($dietPreference->food_allergies ?? '[]', true));
                                            @endphp
                                            @foreach($allergyOptions as $value => $label)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="allergy_{{ $value }}" name="food_allergies[]" 
                                                               value="{{ $value }}" 
                                                               {{ in_array($value, $selectedAllergies) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="allergy_{{ $value }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Preferensi Rasa --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">Preferensi Rasa</h6>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih rasa yang Anda sukai:</label>
                                        <div class="row">
                                            @php
                                                $selectedTastes = old('taste_preferences', json_decode($dietPreference->taste_preferences ?? '[]', true));
                                            @endphp
                                            @foreach($tasteOptions as $value => $label)
                                                <div class="col-md-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="taste_{{ $value }}" name="taste_preferences[]" 
                                                               value="{{ $value }}" 
                                                               {{ in_array($value, $selectedTastes) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="taste_{{ $value }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Preferensi Teknik Masak --}}
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="border-bottom pb-2 mb-3">Preferensi Teknik Masak</h6>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih teknik masak yang Anda sukai:</label>
                                        <div class="row">
                                            @php
                                                $selectedMethods = old('cooking_method_preferences', json_decode($dietPreference->cooking_method_preferences ?? '[]', true));
                                            @endphp
                                            @foreach($cookingMethods as $value => $label)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="method_{{ $value }}" name="cooking_method_preferences[]" 
                                                               value="{{ $value }}" 
                                                               {{ in_array($value, $selectedMethods) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="method_{{ $value }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Profil Kesehatan
                            </button>
                        </form>
                    @endif
                    
                    {{-- TAB PASSWORD --}}
                    @if(request()->query('tab') == 'password')
                        <form action="{{ route('customer.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Lama</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password" required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Ubah Password
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk memperbarui nilai persentase defisit/surplus
        const deficitSurplusSlider = document.getElementById('deficit_surplus_percentage');
        const deficitSurplusValue = document.getElementById('deficit_surplus_value');
        
        if (deficitSurplusSlider && deficitSurplusValue) {
            deficitSurplusSlider.addEventListener('input', function() {
                deficitSurplusValue.textContent = this.value + '%';
            });
        }
        
        // Fungsi untuk menampilkan/menyembunyikan persentase defisit/surplus berdasarkan tujuan diet
        const dietGoalSelect = document.getElementById('diet_goal');
        const deficitSurplusContainer = deficitSurplusSlider?.closest('.mb-3');
        
        if (dietGoalSelect && deficitSurplusContainer) {
            dietGoalSelect.addEventListener('change', function() {
                if (this.value === 'jaga_bb') {
                    deficitSurplusContainer.style.display = 'none';
                } else {
                    deficitSurplusContainer.style.display = 'block';
                }
            });
            
            // Trigger change event pada select saat halaman dimuat
            dietGoalSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
@endsection