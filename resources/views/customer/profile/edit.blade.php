{{-- resources/views/customer/profile/edit.blade.php --}}
@extends('layouts.main')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Profil Saya</h1>
    
    <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    @if($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="{{ $user->name }}" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3" style="width: 150px; height: 150px;">
                            <i class="fa fa-user fa-4x"></i>
                        </div>
                    @endif
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <div class="d-grid">
                        <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-primary">Lihat Pesanan Saya</a>
                    </div>
                </div>
            </div>
            
            {{-- Tambahkan Info BMI jika profil kesehatan sudah diisi --}}
            @if($user->height && $user->weight)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Informasi Kesehatan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>BMI:</span>
                            <span class="fw-bold">{{ $user->getBmi() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Kategori:</span>
                            <span class="fw-bold">{{ $user->getBmiCategory() }}</span>
                        </div>
                        @if($user->dietPreference)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diet:</span>
                                <span class="fw-bold">
                                    @if($user->dietPreference->diet_type == 'regular')
                                        Diet Seimbang
                                    @elseif($user->dietPreference->diet_type == 'low_carb')
                                        Rendah Karbohidrat
                                    @elseif($user->dietPreference->diet_type == 'low_fat')
                                        Rendah Lemak
                                    @elseif($user->dietPreference->diet_type == 'low_sugar')
                                        Rendah Gula
                                    @elseif($user->dietPreference->diet_type == 'low_sodium')
                                        Rendah Garam
                                    @elseif($user->dietPreference->diet_type == 'high_protein')
                                        Tinggi Protein
                                    @elseif($user->dietPreference->diet_type == 'vegetarian')
                                        Vegetarian
                                    @elseif($user->dietPreference->diet_type == 'vegan')
                                        Vegan
                                    @endif
                                </span>
                            </div>
                            @if($user->dietPreference->daily_calorie_target)
                                <div class="d-flex justify-content-between">
                                    <span>Target Kalori:</span>
                                    <span class="fw-bold">{{ number_format($user->dietPreference->daily_calorie_target) }} kkal</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white p-0">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ !request()->query('tab') ? 'active' : '' }}" href="{{ route('customer.profile.edit') }}">
                                <i class="fas fa-user me-1"></i> Informasi Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->query('tab') == 'health' ? 'active' : '' }}" href="{{ route('customer.profile.edit', ['tab' => 'health']) }}">
                                <i class="fas fa-heartbeat me-1"></i> Profil Kesehatan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->query('tab') == 'password' ? 'active' : '' }}" href="{{ route('customer.profile.edit', ['tab' => 'password']) }}">
                                <i class="fas fa-lock me-1"></i> Ubah Password
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(request()->query('tab') == 'health')
                        {{-- Form Profil Kesehatan --}}
                        <form action="{{ route('customer.health-profile.update') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <h5>Data Fisik</h5>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="birthdate" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('birthdate') is-invalid @enderror" id="birthdate" name="birthdate" value="{{ old('birthdate', $user->birthdate?->format('Y-m-d')) }}">
                                        @error('birthdate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                           
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="height" class="form-label">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" class="form-control @error('height') is-invalid @enderror" id="height" name="height" value="{{ old('height', $user->height) }}" min="50" max="250">
                                        @error('height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="weight" class="form-label">Berat Badan (kg) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.1" class="form-control @error('weight') is-invalid @enderror" id="weight" name="weight" value="{{ old('weight', $user->weight) }}" min="20" max="300">
                                        @error('weight')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                @if($user->height && $user->weight)
                                    <div class="alert alert-info">
                                        <strong>BMI Anda:</strong> {{ $user->getBmi() }} 
                                        ({{ $user->getBmiCategory() }})
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <h5>Kondisi Kesehatan</h5>
                                <hr>
                                <p class="text-muted small">Mohon berikan informasi kondisi kesehatan Anda untuk mendapatkan rekomendasi makanan yang tepat.</p>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="has_diabetes" name="has_diabetes" value="1" {{ old('has_diabetes', $healthProfile->has_diabetes) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_diabetes">
                                        Saya memiliki diabetes atau gula darah tinggi
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="has_hypertension" name="has_hypertension" value="1" {{ old('has_hypertension', $healthProfile->has_hypertension) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_hypertension">
                                        Saya memiliki hipertensi (tekanan darah tinggi)
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="has_heart_disease" name="has_heart_disease" value="1" {{ old('has_heart_disease', $healthProfile->has_heart_disease) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_heart_disease">
                                        Saya memiliki penyakit jantung
                                    </label>
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="has_cholesterol" name="has_cholesterol" value="1" {{ old('has_cholesterol', $healthProfile->has_cholesterol) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_cholesterol">
                                        Saya memiliki kolesterol tinggi
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="has_food_allergies" name="has_food_allergies" value="1" {{ old('has_food_allergies', $healthProfile->has_food_allergies) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_food_allergies">
                                        Saya memiliki alergi makanan
                                    </label>
                                </div>
                                
                                <div class="mb-3" id="allergies_detail_container" style="{{ old('has_food_allergies', $healthProfile->has_food_allergies) ? '' : 'display: none;' }}">
                                    <label for="food_allergies_detail" class="form-label">Sebutkan jenis alergi makanan yang Anda miliki:</label>
                                    <textarea class="form-control @error('food_allergies_detail') is-invalid @enderror" id="food_allergies_detail" name="food_allergies_detail" rows="2">{{ old('food_allergies_detail', $healthProfile->food_allergies_detail) }}</textarea>
                                    <div class="form-text">Contoh: kacang, makanan laut, susu, telur, dll.</div>
                                    @error('food_allergies_detail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="other_conditions" class="form-label">Kondisi kesehatan lainnya (opsional):</label>
                                    <textarea class="form-control @error('other_conditions') is-invalid @enderror" id="other_conditions" name="other_conditions" rows="2">{{ old('other_conditions', $healthProfile->other_conditions) }}</textarea>
                                    @error('other_conditions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5>Preferensi Diet</h5>
                                <hr>
                                
                                <div class="mb-3">
                                    <label for="diet_type" class="form-label">Jenis Diet <span class="text-danger">*</span></label>
                                    <select class="form-select @error('diet_type') is-invalid @enderror" id="diet_type" name="diet_type">
                                        <option value="regular" {{ old('diet_type', $dietPreference->diet_type) == 'regular' ? 'selected' : '' }}>Regular (Seimbang)</option>
                                        <option value="low_carb" {{ old('diet_type', $dietPreference->diet_type) == 'low_carb' ? 'selected' : '' }}>Rendah Karbohidrat</option>
                                        <option value="low_fat" {{ old('diet_type', $dietPreference->diet_type) == 'low_fat' ? 'selected' : '' }}>Rendah Lemak</option>
                                        <option value="low_sugar" {{ old('diet_type', $dietPreference->diet_type) == 'low_sugar' ? 'selected' : '' }}>Rendah Gula</option>
                                        <option value="low_sodium" {{ old('diet_type', $dietPreference->diet_type) == 'low_sodium' ? 'selected' : '' }}>Rendah Sodium/Garam</option>
                                        <option value="high_protein" {{ old('diet_type', $dietPreference->diet_type) == 'high_protein' ? 'selected' : '' }}>Tinggi Protein</option>
                                        <option value="vegetarian" {{ old('diet_type', $dietPreference->diet_type) == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                                        <option value="vegan" {{ old('diet_type', $dietPreference->diet_type) == 'vegan' ? 'selected' : '' }}>Vegan</option>
                                    </select>
                                    @error('diet_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="daily_calorie_target" class="form-label">Target Kalori Harian (opsional)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('daily_calorie_target') is-invalid @enderror" id="daily_calorie_target" name="daily_calorie_target" value="{{ old('daily_calorie_target', $dietPreference->daily_calorie_target) }}" min="1000" max="5000">
                                        <span class="input-group-text">kkal</span>
                                    </div>
                                    <div class="form-text">Kosongkan untuk menggunakan perhitungan otomatis berdasarkan data fisik Anda.</div>
                                    @error('daily_calorie_target')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="gluten_free" name="gluten_free" value="1" {{ old('gluten_free', $dietPreference->gluten_free) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gluten_free">
                                        Bebas Gluten (Gluten-Free)
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="dairy_free" name="dairy_free" value="1" {{ old('dairy_free', $dietPreference->dairy_free) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="dairy_free">
                                        Bebas Produk Susu (Dairy-Free)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">Simpan Profil Kesehatan</button>
                            </div>
                        </form>
                        
                    @elseif(request()->query('tab') == 'password')
                        {{-- Form Ubah Password --}}
                        <form action="{{ route('customer.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Ubah Password</button>
                            </div>
                        </form>
                        
                    @else
                        {{-- Form Informasi Pribadi --}}
                        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="profile_picture" class="form-label">Foto Profil</label>
                                <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture">
                                @error('profile_picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($user->profile_picture)
                                    <div class="form-text">Biarkan kosong jika tidak ingin mengganti foto profil.</div>
                                @endif
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle untuk menampilkan detail alergi
    document.addEventListener('DOMContentLoaded', function() {
        const allergiesCheckbox = document.getElementById('has_food_allergies');
        const allergiesContainer = document.getElementById('allergies_detail_container');
        
        if (allergiesCheckbox && allergiesContainer) {
            allergiesCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    allergiesContainer.style.display = 'block';
                } else {
                    allergiesContainer.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush
@endsection